<?php
declare(strict_types=1);

$root = dirname(__DIR__, 4);
require_once $root . '/wp-load.php';

if (!function_exists('imagewebp') || !function_exists('imageavif')) {
    fwrite(STDERR, "PHP GD cần hỗ trợ WebP và AVIF.\n");
    exit(1);
}

$page_id = 61;
$output_dir = dirname(__DIR__) . '/assets/dist/images/home';
if (!is_dir($output_dir) && !wp_mkdir_p($output_dir)) {
    fwrite(STDERR, "Không tạo được thư mục ảnh output.\n");
    exit(1);
}

$groups = array(
    'hero' => array('fields' => array('wpcf-anhbanner-1', 'wpcf-anhbanner-2'), 'widths' => array(480, 768, 1280, 1702)),
    'story' => array('fields' => array('duytv_story_image'), 'widths' => array(480, 768, 1024)),
    'news' => array('fields' => array_merge(
        array_map(fn($i) => 'duytv_news_image_' . $i, range(1, 5)),
        array_map(fn($i) => 'duytv_system_image_' . $i, range(1, 5))
    ), 'widths' => array(480, 768, 1024)),
    'product' => array('fields' => array_map(fn($i) => 'wpcf-anh-san-pham-' . $i, range(1, 8)), 'widths' => array(320, 480, 768)),
    'project' => array('fields' => array_map(fn($i) => 'wpcf-hinh-anh-du-an-' . $i, range(1, 6)), 'widths' => array(320, 480, 768)),
    'partner' => array('fields' => array_map(fn($i) => 'wpcf-doi-tac-' . $i, range(1, 10)), 'widths' => array(160, 320)),
);

function tk_build_image_value(string $field, int $page_id): array
{
    $value = function_exists('get_field') ? get_field($field, $page_id) : get_post_meta($page_id, $field, true);
    $id = 0;
    $url = '';
    if (is_array($value)) {
        $id = (int) ($value['ID'] ?? $value['id'] ?? 0);
        $url = (string) ($value['url'] ?? '');
    } elseif (is_numeric($value)) {
        $id = (int) $value;
    } elseif (is_string($value)) {
        $url = $value;
    }
    if (!$id && $url) $id = (int) attachment_url_to_postid($url);
    $path = $id ? get_attached_file($id) : '';
    return array($id, is_string($path) ? $path : '');
}

function tk_build_open_image(string $path)
{
    $type = @exif_imagetype($path);
    return match ($type) {
        IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
        IMAGETYPE_PNG => @imagecreatefrompng($path),
        IMAGETYPE_GIF => @imagecreatefromgif($path),
        IMAGETYPE_WEBP => @imagecreatefromwebp($path),
        IMAGETYPE_AVIF => @imagecreatefromavif($path),
        default => false,
    };
}

$built = 0;
$skipped = 0;
foreach ($groups as $group) {
    foreach ($group['fields'] as $field) {
        [$id, $source] = tk_build_image_value($field, $page_id);
        if (!$id || !$source || !is_file($source)) {
            continue;
        }
        $dimensions = @getimagesize($source);
        if (!$dimensions) continue;
        [$source_width, $source_height] = $dimensions;
        $source_time = filemtime($source) ?: 0;
        $image = null;
        foreach ($group['widths'] as $requested_width) {
            $target_width = min((int) $requested_width, (int) $source_width);
            $target_height = max(1, (int) round($source_height * ($target_width / $source_width)));
            foreach (array('avif', 'webp') as $format) {
                $destination = $output_dir . '/' . $id . '-' . $requested_width . '.' . $format;
                if (is_file($destination) && (filemtime($destination) ?: 0) >= $source_time && filesize($destination) > 0) {
                    $skipped++;
                    continue;
                }
                if (!$image) {
                    $image = tk_build_open_image($source);
                    if (!$image) break 2;
                }
                $resized = imagecreatetruecolor($target_width, $target_height);
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);
                $ok = $format === 'avif'
                    ? imageavif($resized, $destination, 60)
                    : imagewebp($resized, $destination, 82);
                imagedestroy($resized);
                if (!$ok) {
                    fwrite(STDERR, "Không encode được {$destination}.\n");
                    if ($image) imagedestroy($image);
                    exit(1);
                }
                $built++;
            }
        }
        if ($image) imagedestroy($image);
        echo "{$field}: xong\n";
    }
}

echo "Ảnh responsive: tạo {$built}, bỏ qua {$skipped}.\n";

