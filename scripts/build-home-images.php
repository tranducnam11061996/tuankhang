<?php
declare(strict_types=1);

require_once __DIR__ . '/cli-bootstrap.php';

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
    'hero' => array('fields' => array('tk_home_hero_image', 'duytv_story_image', 'wpcf-anhbanner-1', 'wpcf-anhbanner-2'), 'widths' => array(480, 768, 1200, 1600)),
    'hero-proof' => array(
        'fields' => array('tk_home_hero_secondary_image'),
        'widths' => array(320, 480, 768),
        'fallback_id' => 2250,
        'filename_suffix' => '-proof',
        'crop' => array('x' => 0.16, 'y' => 0.0, 'width' => 0.72, 'height' => 0.78),
    ),
    'story' => array('fields' => array('duytv_story_image'), 'widths' => array(480, 768, 1200, 1600)),
    'capability' => array('fields' => array_merge(
        array('duytv_story_image'),
        array_map(fn($i) => 'duytv_news_image_' . $i, range(1, 5))
    ), 'widths' => array(480, 768, 1200, 1600)),
    'news' => array('fields' => array_merge(
        array_map(fn($i) => 'duytv_news_image_' . $i, range(1, 5)),
        array_map(fn($i) => 'duytv_system_image_' . $i, range(1, 5))
    ), 'widths' => array(320, 480, 768)),
    'product' => array('fields' => array_map(fn($i) => 'wpcf-anh-san-pham-' . $i, range(1, 8)), 'widths' => array(320, 480, 768)),
    'project' => array('fields' => array_map(fn($i) => 'wpcf-hinh-anh-du-an-' . $i, range(1, 6)), 'widths' => array(320, 480, 768)),
    'partner' => array('fields' => array_map(fn($i) => 'wpcf-doi-tac-' . $i, range(1, 10)), 'widths' => array(160, 320)),
);

function tk_build_image_value(string $field, int $page_id, int $fallback_id = 0): array
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
    if (!$id && $fallback_id) $id = $fallback_id;
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
        [$id, $source] = tk_build_image_value($field, $page_id, (int) ($group['fallback_id'] ?? 0));
        if (!$id || !$source || !is_file($source)) {
            continue;
        }
        $dimensions = @getimagesize($source);
        if (!$dimensions) continue;
        [$source_width, $source_height] = $dimensions;
        $crop = $group['crop'] ?? null;
        $source_x = 0;
        $source_y = 0;
        $working_width = $source_width;
        $working_height = $source_height;
        if (is_array($crop)) {
            $source_x = max(0, (int) round($source_width * (float) ($crop['x'] ?? 0)));
            $source_y = max(0, (int) round($source_height * (float) ($crop['y'] ?? 0)));
            $working_width = min($source_width - $source_x, max(1, (int) round($source_width * (float) ($crop['width'] ?? 1))));
            $working_height = min($source_height - $source_y, max(1, (int) round($source_height * (float) ($crop['height'] ?? 1))));
        }
        $source_time = filemtime($source) ?: 0;
        $image = null;
        foreach ($group['widths'] as $requested_width) {
            $target_width = min((int) $requested_width, (int) $working_width);
            $target_height = max(1, (int) round($working_height * ($target_width / $working_width)));
            foreach (array('avif', 'webp') as $format) {
                $destination = $output_dir . '/' . $id . (string) ($group['filename_suffix'] ?? '') . '-' . $requested_width . '.' . $format;
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
                imagecopyresampled($resized, $image, 0, 0, $source_x, $source_y, $target_width, $target_height, $working_width, $working_height);
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

$project_source_dir = dirname(__DIR__) . '/assets/src/images/projects';
$project_thumbnails = array('project-hanam', 'project-dany', 'project-phusan');
foreach ($project_thumbnails as $project_thumbnail) {
    $source = $project_source_dir . '/' . $project_thumbnail . '.png';
    if (!is_file($source)) {
        continue;
    }
    $dimensions = @getimagesize($source);
    if (!$dimensions) {
        continue;
    }
    [$source_width, $source_height] = $dimensions;
    $source_time = filemtime($source) ?: 0;
    $image = null;
    foreach (array(320, 480, 768) as $requested_width) {
        $target_width = min($requested_width, $source_width);
        $target_height = max(1, (int) round($source_height * ($target_width / $source_width)));
        foreach (array('avif', 'webp') as $format) {
            $destination = $output_dir . '/' . $project_thumbnail . '-' . $requested_width . '.' . $format;
            if (is_file($destination) && (filemtime($destination) ?: 0) >= $source_time && filesize($destination) > 0) {
                $skipped++;
                continue;
            }
            if (!$image) {
                $image = tk_build_open_image($source);
                if (!$image) {
                    break 2;
                }
            }
            $resized = imagecreatetruecolor($target_width, $target_height);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);
            $ok = $format === 'avif'
                ? imageavif($resized, $destination, 58)
                : imagewebp($resized, $destination, 80);
            imagedestroy($resized);
            if (!$ok) {
                fwrite(STDERR, "Không encode được {$destination}.\n");
                if ($image) {
                    imagedestroy($image);
                }
                exit(1);
            }
            $built++;
        }
    }
    if ($image) {
        imagedestroy($image);
    }
    echo "{$project_thumbnail}: xong\n";
}

echo "Ảnh responsive: tạo {$built}, bỏ qua {$skipped}.\n";
