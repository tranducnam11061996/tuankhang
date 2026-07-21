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

function tk_product_card($post_id, $compact = false)
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

    echo '<article class="group flex h-full flex-col">';
    echo '<a class="flex aspect-square items-center justify-center overflow-hidden border border-slate-200 bg-white p-3 transition hover:border-primary hover:shadow-card" href="' . esc_url($url) . '" aria-label="' . esc_attr($title) . '">';
    echo $thumbnail_id ? tk_picture($thumbnail_id, 'product-card', array('alt' => $title, 'class' => 'h-full w-full object-contain transition duration-300 group-hover:scale-[1.03]')) : '<span class="text-sm text-slate-400">' . esc_html(tk_home_text('Chưa có ảnh', 'No image')) . '</span>';
    echo '</a><h2 class="mt-3 text-center text-base font-bold leading-6 text-slate-800"><a class="transition hover:text-primary" href="' . esc_url($url) . '">' . esc_html($title) . '</a></h2></article>';
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
            'post__not_in' => array($post_id), 'orderby' => 'rand', 'fields' => 'ids',
            'no_found_rows' => true, 'ignore_sticky_posts' => true,
            'tax_query' => array(array('taxonomy' => 'danh-muc', 'field' => 'term_id', 'terms' => array((int) $term->term_id))),
        ));
        $ids = array_map('intval', $same_term->posts);
    }

    if (count($ids) < $limit) {
        $fallback = new WP_Query(array(
            'post_type' => 'san-pham', 'post_status' => 'publish', 'posts_per_page' => $limit - count($ids),
            'post__not_in' => array_merge(array($post_id), $ids), 'orderby' => 'rand', 'fields' => 'ids',
            'no_found_rows' => true, 'ignore_sticky_posts' => true,
        ));
        $ids = array_merge($ids, array_map('intval', $fallback->posts));
    }

    return array_slice(array_values(array_unique($ids)), 0, $limit);
}
