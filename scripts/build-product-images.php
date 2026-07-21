<?php
declare(strict_types=1);

$root = dirname(__DIR__, 4);
require_once $root . '/wp-load.php';

if (!function_exists('imagewebp') || !function_exists('imageavif')) {
    fwrite(STDERR, "PHP GD cần hỗ trợ WebP và AVIF.\n");
    exit(1);
}

$output_dir = dirname(__DIR__) . '/assets/dist/images/products';
if (!is_dir($output_dir) && !wp_mkdir_p($output_dir)) {
    fwrite(STDERR, "Không tạo được thư mục ảnh sản phẩm.\n");
    exit(1);
}

function tk_product_build_open_image(string $path)
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

function tk_product_build_derivatives(string $source, string $basename, array $widths, string $output_dir): array
{
    $dimensions = @getimagesize($source);
    if (!$dimensions) return array(0, 0);
    [$source_width, $source_height] = $dimensions;
    $source_time = filemtime($source) ?: 0;
    $image = null;
    $built = 0;
    $skipped = 0;

    foreach ($widths as $target_width) {
        if ($target_width > $source_width) continue;
        $target_height = max(1, (int) round($source_height * ($target_width / $source_width)));
        foreach (array('avif', 'webp') as $format) {
            $destination = $output_dir . '/' . $basename . '-' . $target_width . '.' . $format;
            if (is_file($destination) && (filemtime($destination) ?: 0) >= $source_time && filesize($destination) > 0) {
                $skipped++;
                continue;
            }
            if (!$image) {
                $image = tk_product_build_open_image($source);
                if (!$image) break 2;
            }
            $resized = imagecreatetruecolor($target_width, $target_height);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);
            $ok = $format === 'avif' ? imageavif($resized, $destination, 60) : imagewebp($resized, $destination, 82);
            imagedestroy($resized);
            if (!$ok) {
                if ($image) imagedestroy($image);
                throw new RuntimeException("Không encode được {$destination}");
            }
            $built++;
        }
    }
    if ($image) imagedestroy($image);
    return array($built, $skipped);
}

$built = 0;
$skipped = 0;
$banner = dirname(__DIR__) . '/image/background-head-about.png';
[$new, $old] = tk_product_build_derivatives($banner, 'banner', array(480, 768, 1200, 1702), $output_dir);
$built += $new;
$skipped += $old;

$products = get_posts(array(
    'post_type' => 'san-pham',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true,
));

foreach ($products as $post_id) {
    $attachment_id = get_post_thumbnail_id((int) $post_id);
    $source = $attachment_id ? get_attached_file($attachment_id) : '';
    if (!$attachment_id || !is_string($source) || !is_file($source)) continue;
    [$new, $old] = tk_product_build_derivatives($source, (string) $attachment_id, array(160, 320, 480, 768, 1024), $output_dir);
    $built += $new;
    $skipped += $old;
}

echo "Ảnh sản phẩm: tạo {$built}, bỏ qua {$skipped}.\n";
