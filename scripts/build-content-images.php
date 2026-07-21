<?php
declare(strict_types=1);

ini_set('memory_limit', '1024M');

$root = dirname(__DIR__, 4);
require_once $root . '/wp-load.php';

if (!function_exists('imagewebp') || !function_exists('imageavif')) {
    fwrite(STDERR, "PHP GD cần hỗ trợ WebP và AVIF.\n");
    exit(1);
}

$theme_dir = dirname(__DIR__);
$site_dir = $theme_dir . '/assets/dist/images/site';
$content_dir = $theme_dir . '/assets/dist/images/content';
foreach (array($site_dir, $content_dir) as $directory) {
    if (!is_dir($directory) && !wp_mkdir_p($directory)) {
        fwrite(STDERR, "Không tạo được thư mục ảnh: {$directory}\n");
        exit(1);
    }
}

function tk_content_build_open(string $path)
{
    return match (@exif_imagetype($path)) {
        IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
        IMAGETYPE_PNG => @imagecreatefrompng($path),
        IMAGETYPE_GIF => @imagecreatefromgif($path),
        IMAGETYPE_WEBP => @imagecreatefromwebp($path),
        IMAGETYPE_AVIF => @imagecreatefromavif($path),
        default => false,
    };
}

function tk_content_build_derivatives(string $source, string $basename, array $widths, string $output_dir): array
{
    $dimensions = @getimagesize($source);
    if (!$dimensions) return array(0, 0);
    [$source_width, $source_height] = $dimensions;
    $source_time = filemtime($source) ?: 0;
    $image = null; $built = 0; $skipped = 0;
    foreach (array_unique(array_map('intval', $widths)) as $target_width) {
        if ($target_width > $source_width) continue;
        $target_height = max(1, (int) round($source_height * ($target_width / $source_width)));
        foreach (array('avif', 'webp') as $format) {
            $destination = $output_dir . '/' . $basename . '-' . $target_width . '.' . $format;
            if (is_file($destination) && (filemtime($destination) ?: 0) >= $source_time && filesize($destination) > 0) { $skipped++; continue; }
            if (!$image) { $image = tk_content_build_open($source); if (!$image) break 2; }
            $resized = imagecreatetruecolor($target_width, $target_height);
            imagealphablending($resized, false); imagesavealpha($resized, true);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);
            $ok = $format === 'avif' ? imageavif($resized, $destination, 60) : imagewebp($resized, $destination, 82);
            imagedestroy($resized);
            if (!$ok) throw new RuntimeException("Không encode được {$destination}");
            $built++;
        }
    }
    if ($image) imagedestroy($image);
    return array($built, $skipped);
}

$built = 0; $skipped = 0;
[$new, $old] = tk_content_build_derivatives($theme_dir . '/image/background-head-about.png', 'banner', array(480, 768, 1200, 1702), $site_dir);
$built += $new; $skipped += $old;

$post_ids = get_posts(array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true));
$featured_ids = array_values(array_unique(array_filter(array_map('get_post_thumbnail_id', $post_ids))));
foreach ($featured_ids as $attachment_id) {
    $source = get_attached_file((int) $attachment_id);
    if (!is_string($source) || !is_file($source)) continue;
    [$new, $old] = tk_content_build_derivatives($source, (string) $attachment_id, array(160, 320, 480, 768), $content_dir);
    $built += $new; $skipped += $old;
}

$page_ids = get_posts(array('post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true));
$page_image_ids = array();
foreach ($page_ids as $page_id) {
    if (preg_match_all('/wp-image-(\d+)/', (string) get_post_field('post_content', $page_id), $matches)) $page_image_ids = array_merge($page_image_ids, array_map('intval', $matches[1]));
}
foreach (array_values(array_unique($page_image_ids)) as $attachment_id) {
    $source = get_attached_file($attachment_id);
    if (!is_string($source) || !is_file($source)) continue;
    [$new, $old] = tk_content_build_derivatives($source, (string) $attachment_id, array(480, 768, 1200), $content_dir);
    $built += $new; $skipped += $old;
}

echo "Ảnh nội dung: tạo {$built}, bỏ qua {$skipped}.\n";
