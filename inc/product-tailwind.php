<?php
/**
 * Tailwind helpers shared by product archive, taxonomy, search and detail views.
 */

if (!defined('ABSPATH')) {
    exit;
}

function tk_product_primary_term($post_id)
{
    $terms = get_the_terms((int) $post_id, 'danh-muc');
    if (!$terms || is_wp_error($terms)) {
        return null;
    }

    usort($terms, function ($left, $right) {
        $left_depth = count(get_ancestors($left->term_id, 'danh-muc', 'taxonomy'));
        $right_depth = count(get_ancestors($right->term_id, 'danh-muc', 'taxonomy'));
        return $left_depth === $right_depth
            ? $left->term_id <=> $right->term_id
            : $right_depth <=> $left_depth;
    });

    return $terms[0];
}

function tk_product_current_term_ids()
{
    $ids = array();
    if (is_tax('danh-muc')) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            $ids[] = (int) $term->term_id;
            $ids = array_merge($ids, get_ancestors($term->term_id, 'danh-muc', 'taxonomy'));
        }
    } elseif (is_singular('san-pham')) {
        $terms = get_the_terms(get_queried_object_id(), 'danh-muc');
        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $ids[] = (int) $term->term_id;
                $ids = array_merge($ids, get_ancestors($term->term_id, 'danh-muc', 'taxonomy'));
            }
        }
    }
    return array_values(array_unique(array_map('intval', $ids)));
}

function tk_product_listing_title()
{
    if (is_tax('danh-muc')) {
        return single_term_title('', false);
    }
    if (is_search()) {
        return sprintf(
            tk_home_text('Kết quả tìm kiếm cho “%s”', 'Search results for “%s”'),
            get_search_query()
        );
    }
    return tk_home_text('Sản phẩm', 'Products');
}

function tk_product_breadcrumbs($post_id = 0)
{
    $items = array(array(
        'label' => tk_home_text('Trang chủ', 'Home'),
        'url' => home_url('/'),
    ));

    if ($post_id) {
        $term = tk_product_primary_term($post_id);
        if ($term instanceof WP_Term) {
            $ancestors = array_reverse(get_ancestors($term->term_id, 'danh-muc', 'taxonomy'));
            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_term($ancestor_id, 'danh-muc');
                if ($ancestor instanceof WP_Term) {
                    $items[] = array('label' => $ancestor->name, 'url' => get_term_link($ancestor));
                }
            }
            $items[] = array('label' => $term->name, 'url' => get_term_link($term));
        } else {
            $items[] = array('label' => tk_home_text('Sản phẩm', 'Products'), 'url' => get_post_type_archive_link('san-pham'));
        }
        $items[] = array('label' => get_the_title($post_id), 'url' => '');
        return $items;
    }

    if (is_tax('danh-muc')) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            $ancestors = array_reverse(get_ancestors($term->term_id, 'danh-muc', 'taxonomy'));
            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_term($ancestor_id, 'danh-muc');
                if ($ancestor instanceof WP_Term) {
                    $items[] = array('label' => $ancestor->name, 'url' => get_term_link($ancestor));
                }
            }
            $items[] = array('label' => $term->name, 'url' => '');
        }
    } elseif (is_search()) {
        $items[] = array('label' => tk_home_text('Tìm kiếm', 'Search'), 'url' => '');
    } else {
        $items[] = array('label' => tk_home_text('Sản phẩm', 'Products'), 'url' => '');
    }
    return $items;
}

function tk_product_banner($title, $breadcrumbs)
{
    tk_site_banner($title, $breadcrumbs);
}

function tk_product_category_tree($nodes, $instance = 'desktop', $depth = 0)
{
    if (!$nodes) {
        return;
    }
    $active_ids = tk_product_current_term_ids();
    echo '<ul class="' . ($depth ? 'border-l border-slate-200 pl-3' : 'space-y-1') . '">';
    foreach ($nodes as $node) {
        $item = $node['item'];
        $term_id = (int) get_post_meta($item->ID, '_menu_item_object_id', true);
        $active = in_array($term_id, $active_ids, true);
        $panel_id = 'tk-category-' . sanitize_html_class($instance) . '-' . (int) $item->ID;
        echo '<li class="border-b border-slate-200 last:border-0">';
        echo '<div class="flex min-h-11 items-center gap-1">';
        echo '<a class="flex min-h-11 flex-1 items-center py-2 text-sm transition hover:text-accent ' . ($active ? 'font-bold text-primary' : 'text-slate-700') . '" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
        if ($node['children']) {
            $expanded = $active || $depth === 0;
            echo '<button type="button" data-category-toggle aria-expanded="' . ($expanded ? 'true' : 'false') . '" aria-controls="' . esc_attr($panel_id) . '" class="flex size-11 shrink-0 items-center justify-center text-primary" aria-label="' . esc_attr(tk_home_text('Mở danh mục con', 'Toggle subcategory')) . '"><svg class="size-4 transition-transform" aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg></button>';
        }
        echo '</div>';
        if ($node['children']) {
            echo '<div id="' . esc_attr($panel_id) . '"' . ($expanded ? '' : ' hidden') . ' class="pb-2">';
            tk_product_category_tree($node['children'], $instance, $depth + 1);
            echo '</div>';
        }
        echo '</li>';
    }
    echo '</ul>';
}

function tk_product_featured_ids()
{
    static $ids = null;
    if ($ids !== null) {
        return $ids;
    }
    $query = new WP_Query(array(
        'post_type' => 'san-pham',
        'post_status' => 'publish',
        'posts_per_page' => 6,
        'meta_key' => 'wpcf-noi-bat',
        'meta_value' => '1',
        'fields' => 'ids',
        'no_found_rows' => true,
        'ignore_sticky_posts' => true,
    ));
    $ids = array_map('intval', $query->posts);
    return $ids;
}

function tk_product_card($post_id, $compact = false, $heading_level = 'h2')
{
    $post_id = (int) $post_id;
    $title = get_the_title($post_id);
    $url = get_permalink($post_id);
    $thumbnail_id = get_post_thumbnail_id($post_id);
    if ($compact) {
        echo '<article class="border-b border-slate-200 py-3 last:border-0"><a class="group grid grid-cols-[88px_1fr] items-center gap-3" href="' . esc_url($url) . '">';
        echo '<span class="aspect-square overflow-hidden rounded-lg border border-slate-200 bg-white">';
        echo $thumbnail_id ? tk_picture($thumbnail_id, 'product-thumb', array('alt' => $title, 'class' => 'h-full w-full object-contain p-1')) : '<span class="flex h-full items-center justify-center text-xs text-slate-400">No image</span>';
        echo '</span><span><span class="line-clamp-2 font-semibold leading-5 text-slate-800 transition group-hover:text-primary">' . esc_html($title) . '</span><span class="mt-1 block text-xs text-slate-500">' . esc_html(tk_home_text('Đánh giá', 'Rating')) . '</span><span class="block tracking-wider text-amber-400" aria-label="5/5">★★★★★</span></span></a></article>';
        return;
    }

    $heading_level = in_array($heading_level, array('h2', 'h3'), true) ? $heading_level : 'h2';
    echo '<article class="group flex h-full flex-col">';
    echo '<a class="flex aspect-square items-center justify-center overflow-hidden border border-slate-200 bg-white p-3 transition hover:border-primary hover:shadow-card" href="' . esc_url($url) . '" aria-label="' . esc_attr($title) . '">';
    echo $thumbnail_id ? tk_picture($thumbnail_id, 'product-card', array('alt' => $title, 'class' => 'h-full w-full object-contain transition duration-300 group-hover:scale-[1.03]')) : '<span class="text-sm text-slate-400">' . esc_html(tk_home_text('Chưa có ảnh', 'No image')) . '</span>';
    echo '</a><' . $heading_level . ' class="mt-3 text-center text-base font-bold leading-6 text-slate-800"><a class="transition hover:text-primary" href="' . esc_url($url) . '">' . esc_html($title) . '</a></' . $heading_level . '></article>';
}

function tk_product_sidebar($instance = 'desktop')
{
    $menu = tk_home_menu_tree(24);
    ?>
    <div>
        <section aria-labelledby="<?php echo esc_attr($instance); ?>-category-title">
            <h2 id="<?php echo esc_attr($instance); ?>-category-title" class="bg-primary px-4 py-3 text-base font-bold uppercase text-white"><?php echo esc_html(tk_home_text('Danh mục sản phẩm', 'Product categories')); ?></h2>
            <div class="bg-slate-50 px-4 py-2"><?php tk_product_category_tree($menu, $instance); ?></div>
        </section>
        <section class="mt-6" aria-labelledby="<?php echo esc_attr($instance); ?>-featured-title">
            <h2 id="<?php echo esc_attr($instance); ?>-featured-title" class="bg-primary px-4 py-3 text-base font-bold uppercase text-white"><?php echo esc_html(tk_home_text('Sản phẩm nổi bật', 'Featured products')); ?></h2>
            <div class="px-1"><?php foreach (tk_product_featured_ids() as $featured_id) tk_product_card($featured_id, true); ?></div>
        </section>
    </div>
    <?php
}

function tk_product_pagination()
{
    global $wp_query;
    if ((int) $wp_query->max_num_pages <= 1) {
        return;
    }
    $links = paginate_links(array(
        'total' => (int) $wp_query->max_num_pages,
        'current' => max(1, (int) get_query_var('paged')),
        'type' => 'array',
        'prev_text' => tk_home_text('Trước', 'Previous'),
        'next_text' => tk_home_text('Sau', 'Next'),
    ));
    if (!$links) {
        return;
    }
    echo '<nav class="mt-10" aria-label="' . esc_attr(tk_home_text('Phân trang sản phẩm', 'Product pagination')) . '"><ul class="flex flex-wrap items-center justify-center gap-2">';
    foreach ($links as $link) {
        echo '<li class="tk-page-link">' . wp_kses_post($link) . '</li>';
    }
    echo '</ul></nav>';
}

function tk_product_related_ids($post_id, $limit = 4)
{
    $post_id = (int) $post_id;
    $limit = max(1, (int) $limit);
    $ids = array();
    $term = tk_product_primary_term($post_id);

    if ($term instanceof WP_Term) {
        $same_term = new WP_Query(array(
            'post_type' => 'san-pham', 'post_status' => 'publish', 'posts_per_page' => $limit,
            'post__not_in' => array($post_id), 'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC'), 'fields' => 'ids',
            'no_found_rows' => true, 'ignore_sticky_posts' => true,
            'tax_query' => array(array('taxonomy' => 'danh-muc', 'field' => 'term_id', 'terms' => array((int) $term->term_id))),
        ));
        $ids = array_map('intval', $same_term->posts);
    }

    if (count($ids) < $limit) {
        $fallback = new WP_Query(array(
            'post_type' => 'san-pham', 'post_status' => 'publish', 'posts_per_page' => $limit - count($ids),
            'post__not_in' => array_merge(array($post_id), $ids), 'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC'), 'fields' => 'ids',
            'no_found_rows' => true, 'ignore_sticky_posts' => true,
        ));
        $ids = array_merge($ids, array_map('intval', $fallback->posts));
    }

    return array_slice(array_values(array_unique($ids)), 0, $limit);
}

function tk_product_premium_field($key, $post_id)
{
    $post_id = (int) $post_id;
    $value = function_exists('get_field') ? get_field($key, $post_id) : null;
    if ($value === null || $value === false || $value === '') {
        $value = get_post_meta($post_id, $key, true);
    }
    return $value;
}

function tk_product_gallery_items($post_id)
{
    $items = array();
    $seen = array();
    $add = static function ($image) use (&$items, &$seen) {
        $data = tk_home_image_data($image);
        $identity = $data['id'] ? 'id:' . $data['id'] : 'url:' . $data['url'];
        if (!$data['url'] || isset($seen[$identity])) {
            return;
        }
        $seen[$identity] = true;
        $items[] = $data;
    };

    $add(get_post_thumbnail_id((int) $post_id));
    for ($index = 1; $index <= 5; $index++) {
        $add(tk_product_premium_field('tk_product_gallery_image_' . $index, $post_id));
    }
    return $items;
}

function tk_product_premium_highlights($post_id)
{
    $items = array();
    for ($index = 1; $index <= 4; $index++) {
        $title = trim((string) tk_product_premium_field('tk_product_highlight_title_' . $index, $post_id));
        $description = trim((string) tk_product_premium_field('tk_product_highlight_description_' . $index, $post_id));
        if (!$title && !$description) {
            continue;
        }
        $items[] = array(
            'title' => $title ?: sprintf(tk_home_text('Điểm nổi bật %d', 'Highlight %d'), $index),
            'description' => $description,
        );
    }
    return $items;
}

function tk_product_premium_specs($post_id)
{
    $items = array();
    $base = array(
        tk_home_text('Model', 'Model') => get_post_meta($post_id, 'wpcf-model', true),
        tk_home_text('Hãng sản xuất', 'Manufacturer') => get_post_meta($post_id, 'wpcf-hang-sx', true),
        tk_home_text('Xuất xứ', 'Origin') => get_post_meta($post_id, 'wpcf-xuat-xu', true),
    );
    foreach ($base as $label => $value) {
        if (trim((string) $value) !== '') {
            $items[] = array('label' => $label, 'value' => trim((string) $value));
        }
    }
    for ($index = 1; $index <= 8; $index++) {
        $label = trim((string) tk_product_premium_field('tk_product_spec_label_' . $index, $post_id));
        $value = trim((string) tk_product_premium_field('tk_product_spec_value_' . $index, $post_id));
        if ($label && $value) {
            $items[] = array('label' => $label, 'value' => $value);
        }
    }
    return $items;
}

function tk_product_premium_tagline($post_id, $short_description = '')
{
    $tagline = trim((string) tk_product_premium_field('tk_product_tagline', $post_id));
    if ($tagline) {
        return $tagline;
    }

    $term = tk_product_primary_term($post_id);
    $manufacturer = trim((string) get_post_meta($post_id, 'wpcf-hang-sx', true));
    $origin = trim((string) get_post_meta($post_id, 'wpcf-xuat-xu', true));
    $facts = array_values(array_unique(array_filter(array(
        $term instanceof WP_Term ? $term->name : '',
        $manufacturer,
        $origin,
    ))));
    if ($facts) {
        return implode(' · ', $facts);
    }
    return wp_trim_words(wp_strip_all_tags((string) $short_description), 18, '…');
}

function tk_product_catalogue_data($post_id)
{
    $file = tk_product_premium_field('tk_product_catalogue', $post_id);
    if (is_array($file) && !empty($file['url'])) {
        return array(
            'url' => (string) $file['url'],
            'title' => (string) ($file['title'] ?? basename((string) $file['url'])),
        );
    }
    if (is_numeric($file)) {
        $url = wp_get_attachment_url((int) $file);
        if ($url) {
            return array('url' => $url, 'title' => get_the_title((int) $file));
        }
    }
    if (is_string($file) && filter_var($file, FILTER_VALIDATE_URL)) {
        return array('url' => $file, 'title' => basename((string) wp_parse_url($file, PHP_URL_PATH)));
    }
    return array();
}

function tk_product_video_embed_url($post_id)
{
    $url = trim((string) tk_product_premium_field('tk_product_video_url', $post_id));
    if (!$url) {
        return '';
    }
    $parts = wp_parse_url($url);
    $host = strtolower((string) ($parts['host'] ?? ''));
    $path = trim((string) ($parts['path'] ?? ''), '/');
    if (strpos($host, 'youtu.be') !== false) {
        $video_id = preg_replace('/[^A-Za-z0-9_-]/', '', explode('/', $path)[0] ?? '');
        return $video_id ? 'https://www.youtube-nocookie.com/embed/' . $video_id . '?autoplay=1&rel=0' : '';
    }
    if (strpos($host, 'youtube.com') !== false) {
        parse_str((string) ($parts['query'] ?? ''), $query);
        $video_id = preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($query['v'] ?? ''));
        if (!$video_id && strpos($path, 'embed/') === 0) {
            $video_id = preg_replace('/[^A-Za-z0-9_-]/', '', substr($path, 6));
        }
        return $video_id ? 'https://www.youtube-nocookie.com/embed/' . $video_id . '?autoplay=1&rel=0' : '';
    }
    if (strpos($host, 'vimeo.com') !== false) {
        $video_id = preg_replace('/\D+/', '', $path);
        return $video_id ? 'https://player.vimeo.com/video/' . $video_id . '?autoplay=1&dnt=1' : '';
    }
    return '';
}

function tk_product_normalize_rich_content($post_id)
{
    $raw = (string) get_post_field('post_content', (int) $post_id);
    $content = apply_filters('the_content', $raw);
    if (!$content) {
        return '';
    }

    if (class_exists('WP_HTML_Tag_Processor')) {
        $processor = new WP_HTML_Tag_Processor($content);
        while ($processor->next_tag()) {
            $tag = strtoupper((string) $processor->get_tag());
            $processor->remove_attribute('style');
            $processor->remove_attribute('align');
            if ($tag === 'IMG') {
                $processor->set_attribute('loading', 'lazy');
                $processor->set_attribute('decoding', 'async');
                $processor->set_attribute('fetchpriority', 'auto');
            }
        }
        $content = $processor->get_updated_html();
    }

    $content = preg_replace_callback(
        '/(?:<p(?:\s[^>]*)?>\s*-\s*.*?<\/p>\s*){2,}/is',
        static function ($match) {
            preg_match_all('/<p(?:\s[^>]*)?>\s*-\s*(.*?)<\/p>/is', $match[0], $items);
            if (empty($items[1])) {
                return $match[0];
            }
            return '<ul class="tk-product-generated-list"><li>' . implode('</li><li>', $items[1]) . '</li></ul>';
        },
        $content
    );

    $content = preg_replace_callback(
        '/<img\b[^>]*class="[^"]*wp-image-(\d+)[^"]*"[^>]*>/i',
        static function ($match) {
            $attachment_id = (int) $match[1];
            if (!wp_get_attachment_url($attachment_id)) {
                return $match[0];
            }
            return tk_picture(
                $attachment_id,
                'product-content',
                array(
                    'alt' => (string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true),
                    'class' => 'tk-product-content-image',
                )
            );
        },
        $content
    );

    $markers = array('Quý Nha khoa muốn', 'Mọi chi tiết', 'For more information', 'Please contact');
    foreach ($markers as $marker) {
        $marker_position = stripos($content, $marker);
        if ($marker_position === false) {
            continue;
        }
        $start = strripos(substr($content, 0, $marker_position), '<p');
        $tail = $start !== false ? substr($content, $start) : '';
        if ($tail && (preg_match('/[\w.+-]+@[\w.-]+\.[A-Za-z]{2,}/', wp_strip_all_tags($tail)) || stripos($tail, 'tel:') !== false)) {
            $content = substr($content, 0, $start) . '<aside class="tk-product-legacy-contact">' . $tail . '</aside>';
        }
        break;
    }

    return wp_kses_post($content);
}

function tk_product_preload_hero()
{
    if (!is_singular('san-pham')) {
        return;
    }
    $gallery = tk_product_gallery_items(get_queried_object_id());
    $image = $gallery[0] ?? array();
    if (empty($image['url'])) {
        return;
    }
    $srcset = array();
    foreach (array(480, 768, 1024) as $width) {
        $relative = 'assets/dist/images/products/' . (int) ($image['id'] ?? 0) . '-' . $width . '.avif';
        if (!empty($image['id']) && is_file(get_theme_file_path($relative))) {
            $srcset[] = get_theme_file_uri($relative) . ' ' . $width . 'w';
        }
    }
    echo '<link rel="preload" as="image" href="' . esc_url($image['url']) . '"';
    if ($srcset) {
        echo ' imagesrcset="' . esc_attr(implode(', ', $srcset)) . '" imagesizes="(min-width: 1024px) 42vw, calc(100vw - 32px)" type="image/avif"';
    }
    echo ' fetchpriority="high">' . "\n";
}
add_action('wp_head', 'tk_product_preload_hero', 3);
