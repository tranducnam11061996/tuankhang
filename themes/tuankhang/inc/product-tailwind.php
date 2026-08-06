<?php
/**
 * Tailwind helpers shared by product archive, taxonomy, search and detail views.
 */

if (!defined('ABSPATH')) {
    exit;
}

function tk_product_allowed_sorts()
{
    return array('default', 'title_asc', 'title_desc', 'newest');
}

function tk_product_current_sort()
{
    $sort = get_query_var('tk_product_sort');
    if (!$sort && isset($_GET['tk_product_sort']) && is_string($_GET['tk_product_sort'])) {
        $sort = sanitize_key(wp_unslash($_GET['tk_product_sort']));
    }
    return in_array($sort, tk_product_allowed_sorts(), true) ? $sort : 'default';
}

function tk_product_register_query_vars($vars)
{
    $vars[] = 'tk_product_sort';
    return $vars;
}
add_filter('query_vars', 'tk_product_register_query_vars');

function tk_product_apply_listing_sort($query)
{
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    $post_type = $query->get('post_type');
    $is_product_search = $query->is_search() && (
        $post_type === 'san-pham'
        || (is_array($post_type) && in_array('san-pham', $post_type, true))
    );
    if (!$query->is_post_type_archive('san-pham') && !$query->is_tax('danh-muc') && !$is_product_search) {
        return;
    }

    $query->set('posts_per_page', 12);

    $sort = sanitize_key((string) $query->get('tk_product_sort'));
    if (!in_array($sort, tk_product_allowed_sorts(), true)) {
        $sort = 'default';
        $query->set('tk_product_sort', $sort);
    }
    if ($sort === 'title_asc' || $sort === 'title_desc') {
        $query->set('orderby', 'title');
        $query->set('order', $sort === 'title_asc' ? 'ASC' : 'DESC');
    } elseif ($sort === 'newest') {
        $query->set('orderby', 'date');
        $query->set('order', 'DESC');
    }
}
add_action('pre_get_posts', 'tk_product_apply_listing_sort', 20);

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

function tk_product_current_term_id()
{
    $term = is_tax('danh-muc') ? get_queried_object() : null;
    return $term instanceof WP_Term ? (int) $term->term_id : 0;
}

function tk_product_term_counts()
{
    static $counts = null;
    if ($counts !== null) {
        return $counts;
    }
    $counts = array();
    $terms = get_terms(array(
        'taxonomy' => 'danh-muc',
        'hide_empty' => false,
        'pad_counts' => true,
    ));
    if (!is_wp_error($terms)) {
        foreach ($terms as $term) {
            $counts[(int) $term->term_id] = (int) $term->count;
        }
    }
    return $counts;
}

function tk_product_menu_term($item)
{
    $term_id = (int) get_post_meta($item->ID, '_menu_item_object_id', true);
    $term = $term_id ? get_term($term_id, 'danh-muc') : null;
    return $term instanceof WP_Term ? $term : null;
}

function tk_product_category_icon($slug)
{
    $slug = sanitize_title((string) $slug);
    if (strpos($slug, 'implant') !== false) {
        return '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M8.5 3.5c1.3 0 2.1.8 3.5.8s2.2-.8 3.5-.8c2.6 0 4 2.1 3.5 4.8-.8 4.2-2.2 11.8-4.5 11.8-1.5 0-1.2-4.7-2.5-4.7s-1 4.7-2.5 4.7C7.2 20.1 5.8 12.5 5 8.3c-.5-2.7.9-4.8 3.5-4.8Z"/><path d="M9.2 8.2h5.6M9.8 11h4.4"/></svg>';
    }
    if (strpos($slug, 'thiet-bi') !== false || strpos($slug, 'device') !== false) {
        return '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="5" width="16" height="12" rx="2"/><path d="M8 21h8M12 17v4M8 9h2l1.2 4 1.7-7 1.3 5H17"/></svg>';
    }
    if (strpos($slug, 'vat-lieu') !== false || strpos($slug, 'material') !== false) {
        return '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m12 3 8 4.5-8 4.5-8-4.5L12 3Z"/><path d="m4 12 8 4.5 8-4.5M4 16.5 12 21l8-4.5"/></svg>';
    }
    if (strpos($slug, 'den') !== false || strpos($slug, 'light') !== false) {
        return '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M9 15h6M10 19h4M8.3 13.2a6 6 0 1 1 7.4 0c-.8.6-1.2 1.1-1.3 1.8H9.6c-.1-.7-.5-1.2-1.3-1.8Z"/><path d="M12 1v2M3.5 4.5 5 6M21 4.5 19 6"/></svg>';
    }
    return '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></svg>';
}

function tk_product_listing_base_url()
{
    if (is_tax('danh-muc')) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            $url = get_term_link($term);
            if (!is_wp_error($url)) {
                return $url;
            }
        }
    }
    return get_post_type_archive_link('san-pham') ?: home_url('/san-pham/');
}

function tk_product_listing_context()
{
    global $wp_query;
    $found = isset($wp_query->found_posts) ? (int) $wp_query->found_posts : 0;
    $hero_product_id = !empty($wp_query->posts[0]) ? (int) $wp_query->posts[0]->ID : 0;
    $context = array(
        'eyebrow' => tk_home_text('Danh mục sản phẩm', 'Product catalogue'),
        'title' => tk_home_text('Giải pháp nha khoa cho thực hành hiện đại', 'Dental solutions for modern practice'),
        'description' => tk_home_text(
            'Khám phá hệ sinh thái Implant, thiết bị và vật liệu nha khoa được Tuấn Khang lựa chọn để hỗ trợ nhu cầu lâm sàng và vận hành.',
            'Explore an ecosystem of implants, equipment and dental materials selected by Tuan Khang for clinical and operational needs.'
        ),
        'found' => $found,
        'hero_product_id' => $hero_product_id,
    );
    if (is_tax('danh-muc')) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            $context['title'] = $term->name;
            $description = trim(wp_strip_all_tags(term_description($term)));
            $context['description'] = $description ?: sprintf(
                tk_home_text('Khám phá các giải pháp %s được tuyển chọn cho nhu cầu chuyên môn nha khoa.', 'Explore selected %s solutions for professional dental needs.'),
                $term->name
            );
        }
    } elseif (is_search()) {
        $query = get_search_query();
        $context['eyebrow'] = tk_home_text('Tìm kiếm sản phẩm', 'Product search');
        $context['title'] = sprintf(tk_home_text('Kết quả cho “%s”', 'Results for “%s”'), $query);
        $context['description'] = sprintf(
            tk_home_text('Tìm thấy %s sản phẩm phù hợp với từ khóa của bạn.', 'Found %s products matching your keyword.'),
            number_format_i18n($found)
        );
    }
    return $context;
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
    $exact_id = tk_product_current_term_id();
    $counts = tk_product_term_counts();
    $has_active_root = $depth === 0 && !empty(array_intersect($active_ids, array_map(static function ($node) {
        return (int) get_post_meta($node['item']->ID, '_menu_item_object_id', true);
    }, $nodes)));
    echo '<ul class="' . ($depth ? 'tk-catalog-subcategory-list' : 'tk-catalog-category-list') . '"' . ($depth ? '' : ' data-category-tree') . '>';
    foreach ($nodes as $index => $node) {
        $item = $node['item'];
        $term = tk_product_menu_term($item);
        $term_id = $term instanceof WP_Term ? (int) $term->term_id : 0;
        $active_path = $term_id && in_array($term_id, $active_ids, true);
        $is_current = $term_id && $term_id === $exact_id;
        $panel_id = 'tk-category-' . sanitize_html_class($instance) . '-' . (int) $item->ID;
        if ($depth > 0) {
            echo '<li><a class="tk-catalog-subcategory-link' . ($is_current ? ' is-current' : '') . '" href="' . esc_url($item->url) . '"' . ($is_current ? ' aria-current="page"' : '') . '><span>' . esc_html($item->title) . '</span><small>' . esc_html(number_format_i18n($counts[$term_id] ?? 0)) . '</small></a>';
            if ($node['children']) {
                tk_product_category_tree($node['children'], $instance, $depth + 1);
            }
            echo '</li>';
            continue;
        }

        $expanded = !empty($node['children']) && ($active_path || (!$has_active_root && $index === 0));
        $slug = $term instanceof WP_Term ? $term->slug : sanitize_title($item->title);
        echo '<li class="tk-catalog-category' . ($active_path ? ' is-active-path' : '') . ($is_current ? ' is-current' : '') . '">';
        echo '<div class="tk-catalog-category-row">';
        echo '<a class="tk-catalog-category-link" href="' . esc_url($item->url) . '"' . ($is_current ? ' aria-current="page"' : '') . '>';
        echo '<span class="tk-catalog-category-index">' . esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) . '</span>';
        echo '<span class="tk-catalog-category-icon">' . tk_product_category_icon($slug) . '</span>';
        echo '<span class="tk-catalog-category-copy"><strong>' . esc_html($item->title) . '</strong><small>' . sprintf(esc_html(tk_home_text('%s sản phẩm', '%s products')), esc_html(number_format_i18n($counts[$term_id] ?? 0))) . '</small></span>';
        echo '</a>';
        if ($node['children']) {
            echo '<button type="button" data-category-toggle aria-expanded="' . ($expanded ? 'true' : 'false') . '" aria-controls="' . esc_attr($panel_id) . '" class="tk-catalog-category-toggle" aria-label="' . esc_attr(sprintf(tk_home_text('Mở hoặc đóng danh mục %s', 'Toggle %s category'), $item->title)) . '"><svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m6 8 4 4 4-4"/></svg></button>';
        }
        echo '</div>';
        if ($node['children']) {
            echo '<div id="' . esc_attr($panel_id) . '" class="tk-catalog-category-panel"' . ($expanded ? '' : ' hidden') . '>';
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
        'posts_per_page' => -1,
        'meta_key' => 'wpcf-noi-bat',
        'meta_value' => '1',
        'orderby' => array('date' => 'DESC', 'ID' => 'DESC'),
        'fields' => 'ids',
        'no_found_rows' => true,
        'ignore_sticky_posts' => true,
    ));
    $ids = array_map('intval', $query->posts);
    $positions = array_flip($ids);
    $orders = array();
    foreach ($ids as $featured_id) {
        $orders[$featured_id] = tk_product_normalize_featured_order(get_post_meta($featured_id, '_tk_featured_order', true));
    }
    usort($ids, static function ($left_id, $right_id) use ($orders, $positions) {
        $left_order = $orders[$left_id] ?? 0;
        $right_order = $orders[$right_id] ?? 0;
        if ($left_order && $right_order && $left_order !== $right_order) {
            return $left_order <=> $right_order;
        }
        if ($left_order !== $right_order) {
            return $left_order ? -1 : 1;
        }
        return ($positions[$left_id] ?? 0) <=> ($positions[$right_id] ?? 0);
    });
    $ids = array_slice($ids, 0, 8);
    return $ids;
}

function tk_product_normalize_featured_order($value)
{
    if (is_array($value)) {
        return 0;
    }
    $value = trim((string) $value);
    if ($value === '' || !ctype_digit($value)) {
        return 0;
    }
    $order = (int) $value;
    return $order >= 1 && $order <= 999 ? $order : 0;
}

function tk_product_register_featured_order_meta_box()
{
    add_meta_box(
        'tk-product-featured-order',
        'Thứ tự nổi bật',
        'tk_product_render_featured_order_meta_box',
        'san-pham',
        'side',
        'default'
    );
}
add_action('add_meta_boxes_san-pham', 'tk_product_register_featured_order_meta_box');

function tk_product_render_featured_order_meta_box($post)
{
    $order = tk_product_normalize_featured_order(get_post_meta($post->ID, '_tk_featured_order', true));
    wp_nonce_field('tk_product_featured_order', 'tk_product_featured_order_nonce');
    ?>
    <p><label for="tk-featured-order"><strong>Vị trí hiển thị</strong></label></p>
    <input id="tk-featured-order" name="tk_featured_order" type="number" min="1" max="999" step="1" value="<?php echo $order ? esc_attr((string) $order) : ''; ?>" class="widefat" inputmode="numeric">
    <p class="description">Số nhỏ hiển thị trước. Để trống nếu chưa cần ưu tiên. Chỉ áp dụng khi sản phẩm được đánh dấu nổi bật.</p>
    <?php
}

function tk_product_save_featured_order($post_id)
{
    if (get_post_type($post_id) !== 'san-pham'
        || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        || wp_is_post_autosave($post_id)
        || wp_is_post_revision($post_id)
        || !current_user_can('edit_post', $post_id)
        || !isset($_POST['tk_product_featured_order_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tk_product_featured_order_nonce'])), 'tk_product_featured_order')) {
        return;
    }

    $value = isset($_POST['tk_featured_order']) ? wp_unslash($_POST['tk_featured_order']) : '';
    $order = tk_product_normalize_featured_order($value);
    if ($order) {
        update_post_meta($post_id, '_tk_featured_order', $order);
    } else {
        delete_post_meta($post_id, '_tk_featured_order');
    }
}
add_action('save_post_san-pham', 'tk_product_save_featured_order');

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
        echo '</span><span><span class="line-clamp-2 font-semibold leading-5 text-slate-800 transition group-hover:text-primary">' . esc_html($title) . '</span><span class="mt-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">' . esc_html(tk_home_text('Xem sản phẩm', 'View product')) . '</span></span></a></article>';
        return;
    }

    $heading_level = in_array($heading_level, array('h2', 'h3'), true) ? $heading_level : 'h2';
    echo '<article class="group flex h-full flex-col">';
    echo '<a class="flex aspect-square items-center justify-center overflow-hidden border border-slate-200 bg-white p-3 transition hover:border-primary hover:shadow-card" href="' . esc_url($url) . '" aria-label="' . esc_attr($title) . '">';
    echo $thumbnail_id ? tk_picture($thumbnail_id, 'product-card', array('alt' => $title, 'class' => 'h-full w-full object-contain transition duration-300 group-hover:scale-[1.03]')) : '<span class="text-sm text-slate-400">' . esc_html(tk_home_text('Chưa có ảnh', 'No image')) . '</span>';
    echo '</a><' . $heading_level . ' class="mt-3 text-center text-base font-bold leading-6 text-slate-800"><a class="transition hover:text-primary" href="' . esc_url($url) . '">' . esc_html($title) . '</a></' . $heading_level . '></article>';
}

function tk_product_listing_card($post_id, $variant = 'catalog', $display_index = 0)
{
    $post_id = (int) $post_id;
    $title = get_the_title($post_id);
    $url = get_permalink($post_id);
    $thumbnail_id = get_post_thumbnail_id($post_id);
    $term = tk_product_primary_term($post_id);
    $manufacturer = trim((string) get_post_meta($post_id, 'wpcf-hang-sx', true));
    $origin = trim((string) get_post_meta($post_id, 'wpcf-xuat-xu', true));
    $facts = array_values(array_unique(array_filter(array($manufacturer, $origin))));
    $variant = $variant === 'featured' ? 'featured' : 'catalog';
    $display_index = max(0, (int) $display_index);
    $card_number = $variant === 'featured' && $display_index
        ? $display_index
        : $post_id % 100;
    ?>
    <article class="tk-catalog-card tk-catalog-card--<?php echo esc_attr($variant); ?>"<?php echo $variant === 'catalog' ? ' data-reveal' : ''; ?>>
        <a href="<?php echo esc_url($url); ?>">
            <span class="tk-catalog-card-stage">
                <?php if ($thumbnail_id) : ?>
                    <?php echo tk_picture($thumbnail_id, 'product-card', array('alt' => $title, 'class' => 'tk-catalog-card-image')); ?>
                <?php else : ?>
                    <span class="tk-catalog-card-placeholder" aria-hidden="true"><?php echo tk_product_category_icon(''); ?></span>
                <?php endif; ?>
                <span class="tk-catalog-card-number" aria-hidden="true"><?php echo esc_html(str_pad((string) $card_number, 2, '0', STR_PAD_LEFT)); ?></span>
            </span>
            <span class="tk-catalog-card-body">
                <span class="tk-catalog-card-eyebrow"><?php echo esc_html($term instanceof WP_Term ? $term->name : tk_home_text('Thiết bị nha khoa', 'Dental solution')); ?></span>
                <h3><?php echo esc_html($title); ?></h3>
                <?php if ($facts) : ?><span class="tk-catalog-card-meta"><?php echo esc_html(implode(' · ', $facts)); ?></span><?php endif; ?>
                <span class="tk-catalog-card-cta"><span class="tk-catalog-card-cta-label"><?php echo esc_html(tk_home_text('Khám phá sản phẩm', 'Explore product')); ?></span><svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h12M11 5l5 5-5 5"/></svg></span>
            </span>
        </a>
    </article>
    <?php
}

function tk_product_sidebar($instance = 'desktop')
{
    $menu = tk_home_menu_tree(24);
    ?>
    <div class="tk-catalog-console">
        <div class="tk-catalog-console-head">
            <span><?php echo esc_html(tk_home_text('Catalog / 2026', 'Catalogue / 2026')); ?></span>
            <span class="tk-catalog-console-status"><i></i><?php echo esc_html(tk_home_text('Trực tuyến', 'Online')); ?></span>
        </div>
        <section aria-labelledby="<?php echo esc_attr($instance); ?>-category-title">
            <p class="tk-catalog-kicker"><?php echo esc_html(tk_home_text('Điều hướng chuyên môn', 'Professional navigation')); ?></p>
            <h2 id="<?php echo esc_attr($instance); ?>-category-title"><?php echo esc_html(tk_home_text('Danh mục sản phẩm', 'Product categories')); ?></h2>
            <?php tk_product_category_tree($menu, $instance); ?>
        </section>
        <a class="tk-catalog-all-link" href="<?php echo esc_url(get_post_type_archive_link('san-pham')); ?>">
            <span><?php echo esc_html(tk_home_text('Xem toàn bộ catalog', 'View full catalogue')); ?></span>
            <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h12M11 5l5 5-5 5"/></svg>
        </a>
        <div class="tk-catalog-consult">
            <span class="tk-catalog-consult-icon" aria-hidden="true">TK</span>
            <div><strong><?php echo esc_html(tk_home_text('Cần tư vấn cấu hình?', 'Need configuration advice?')); ?></strong><p><?php echo esc_html(tk_home_text('Đội ngũ chuyên môn sẵn sàng hỗ trợ.', 'Our specialists are ready to help.')); ?></p></div>
            <a href="<?php echo esc_url(home_url('/lien-he/')); ?>" aria-label="<?php echo esc_attr(tk_home_text('Liên hệ tư vấn', 'Contact for advice')); ?>"><svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h12M11 5l5 5-5 5"/></svg></a>
        </div>
    </div>
    <?php
}

function tk_product_featured_strip()
{
    $ids = tk_product_featured_ids();
    if (!$ids) {
        return;
    }
    ?>
    <section class="tk-catalog-featured" aria-labelledby="tk-catalog-featured-title">
        <div class="tk-container">
            <div class="tk-catalog-featured-head">
                <div><p><?php echo esc_html(tk_home_text('Tuyển chọn bởi Tuấn Khang', 'Selected by Tuan Khang')); ?></p><h2 id="tk-catalog-featured-title"><?php echo esc_html(tk_home_text('Thiết bị nổi bật cho thực hành hiện đại', 'Featured equipment for modern practice')); ?></h2></div>
                <span><?php echo esc_html(sprintf(tk_home_text('%s giải pháp', '%s solutions'), str_pad((string) count($ids), 2, '0', STR_PAD_LEFT))); ?></span>
            </div>
            <div class="tk-catalog-featured-grid">
                <?php foreach ($ids as $index => $featured_id) tk_product_listing_card($featured_id, 'featured', $index + 1); ?>
            </div>
        </div>
    </section>
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
        'add_args' => array_filter(array(
            's' => get_search_query(),
            'post_type' => is_search() ? 'san-pham' : '',
            'tk_product_sort' => tk_product_current_sort() !== 'default' ? tk_product_current_sort() : '',
        )),
    ));
    if (!$links) {
        return;
    }
    echo '<nav class="tk-catalog-pagination" aria-label="' . esc_attr(tk_home_text('Phân trang sản phẩm', 'Product pagination')) . '"><ul>';
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

/**
 * Return repeater rows when the dynamic parent meta is authoritative.
 *
 * A null return means the repeater has never been initialized for this post,
 * so callers may safely fall back to legacy fixed-slot meta.
 */
function tk_product_premium_repeater_rows($field_name, $post_id)
{
    $post_id = (int) $post_id;
    if (!metadata_exists('post', $post_id, $field_name)) {
        return null;
    }

    $rows = function_exists('get_field') ? get_field($field_name, $post_id) : array();
    return is_array($rows) ? $rows : array();
}

/**
 * Return ordered gallery attachment IDs from the active product schema.
 */
function tk_product_gallery_attachment_ids($post_id)
{
    $post_id = (int) $post_id;
    $ids = array();
    $rows = tk_product_premium_repeater_rows('tk_product_gallery', $post_id);

    if ($rows !== null) {
        foreach ($rows as $row) {
            $image = is_array($row) ? ($row['tk_product_gallery_image'] ?? 0) : 0;
            if (is_array($image)) {
                $image = $image['ID'] ?? ($image['id'] ?? 0);
            }
            $image_id = (int) $image;
            if ($image_id > 0) {
                $ids[] = $image_id;
            }
        }
    } else {
        for ($index = 1; $index <= 5; $index++) {
            $image = tk_product_premium_field('tk_product_gallery_image_' . $index, $post_id);
            if (is_array($image)) {
                $image = $image['ID'] ?? ($image['id'] ?? 0);
            }
            $image_id = (int) $image;
            if ($image_id > 0) {
                $ids[] = $image_id;
            }
        }
    }

    return array_values(array_unique($ids));
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
    foreach (tk_product_gallery_attachment_ids($post_id) as $attachment_id) {
        $add($attachment_id);
    }
    return $items;
}

function tk_product_premium_highlights($post_id)
{
    $items = array();
    $rows = tk_product_premium_repeater_rows('tk_product_highlights', $post_id);

    if ($rows === null) {
        $rows = array();
        for ($index = 1; $index <= 4; $index++) {
            $rows[] = array(
                'tk_product_highlight_title' => tk_product_premium_field('tk_product_highlight_title_' . $index, $post_id),
                'tk_product_highlight_description' => tk_product_premium_field('tk_product_highlight_description_' . $index, $post_id),
            );
        }
    }

    foreach ($rows as $index => $row) {
        if (!is_array($row)) {
            continue;
        }
        $title = trim((string) ($row['tk_product_highlight_title'] ?? ''));
        $description = trim((string) ($row['tk_product_highlight_description'] ?? ''));
        if (!$title && !$description) {
            continue;
        }
        $items[] = array(
            'title' => $title ?: sprintf(tk_home_text('Điểm nổi bật %d', 'Highlight %d'), $index + 1),
            'description' => $description,
        );
    }
    return $items;
}

function tk_product_premium_specs($post_id)
{
    $items = array();
    $base = array(
        array(
            'type' => 'model',
            'label' => tk_home_text('Model', 'Model'),
            'value' => get_post_meta($post_id, 'wpcf-model', true),
        ),
        array(
            'type' => 'manufacturer',
            'label' => tk_home_text('Hãng sản xuất', 'Manufacturer'),
            'value' => get_post_meta($post_id, 'wpcf-hang-sx', true),
        ),
        array(
            'type' => 'origin',
            'label' => tk_home_text('Xuất xứ', 'Origin'),
            'value' => get_post_meta($post_id, 'wpcf-xuat-xu', true),
        ),
    );
    foreach ($base as $spec) {
        if (trim((string) $spec['value']) !== '') {
            $items[] = array(
                'type' => $spec['type'],
                'label' => $spec['label'],
                'value' => trim((string) $spec['value']),
            );
        }
    }
    $rows = tk_product_premium_repeater_rows('tk_product_specs', $post_id);
    if ($rows === null) {
        $rows = array();
        for ($index = 1; $index <= 8; $index++) {
            $rows[] = array(
                'tk_product_spec_label' => tk_product_premium_field('tk_product_spec_label_' . $index, $post_id),
                'tk_product_spec_value' => tk_product_premium_field('tk_product_spec_value_' . $index, $post_id),
            );
        }
    }

    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $label = trim((string) ($row['tk_product_spec_label'] ?? ''));
        $value = trim((string) ($row['tk_product_spec_value'] ?? ''));
        if ($label && $value) {
            $items[] = array('type' => 'data', 'label' => $label, 'value' => $value);
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

/**
 * Return a cached Vimeo oEmbed thumbnail URL without making every page view
 * wait on a remote request.
 */
function tk_product_vimeo_thumbnail_url($video_id)
{
    $video_id = preg_replace('/\D+/', '', (string) $video_id);
    if (!$video_id) {
        return '';
    }

    $cache_key = 'tk_vimeo_thumb_' . $video_id;
    $cached = get_transient($cache_key);
    if (is_array($cached) && array_key_exists('url', $cached)) {
        return (string) $cached['url'];
    }

    $endpoint = add_query_arg(
        'url',
        'https://vimeo.com/' . $video_id,
        'https://vimeo.com/api/oembed.json'
    );
    $response = wp_safe_remote_get(
        $endpoint,
        array(
            'timeout' => 3,
            'redirection' => 2,
            'user-agent' => 'TuanKhangMedical/1.0; ' . home_url('/'),
        )
    );

    $thumbnail_url = '';
    if (!is_wp_error($response) && 200 === (int) wp_remote_retrieve_response_code($response)) {
        $payload = json_decode((string) wp_remote_retrieve_body($response), true);
        if (is_array($payload) && !empty($payload['thumbnail_url'])) {
            $candidate = esc_url_raw((string) $payload['thumbnail_url']);
            if (wp_http_validate_url($candidate)) {
                $thumbnail_url = $candidate;
            }
        }
    }

    set_transient(
        $cache_key,
        array('url' => $thumbnail_url),
        $thumbnail_url ? DAY_IN_SECONDS : HOUR_IN_SECONDS
    );
    return $thumbnail_url;
}

/**
 * Normalize product video data for the template and click-to-play component.
 *
 * @return array{provider:string,video_id:string,embed_url:string,poster_id:int,poster_url:string,poster_fallbacks:array<int,string>}|array{}
 */
function tk_product_video_data($post_id)
{
    $url = trim((string) tk_product_premium_field('tk_product_video_url', $post_id));
    if (!$url) {
        return array();
    }

    $parts = wp_parse_url($url);
    $host = strtolower((string) ($parts['host'] ?? ''));
    $host = preg_replace('/^www\./', '', $host);
    $path = trim((string) ($parts['path'] ?? ''), '/');
    $provider = '';
    $video_id = '';

    if ('youtu.be' === $host) {
        $provider = 'youtube';
        $video_id = explode('/', $path)[0] ?? '';
    } elseif (in_array($host, array('youtube.com', 'm.youtube.com', 'music.youtube.com', 'youtube-nocookie.com'), true)) {
        $provider = 'youtube';
        parse_str((string) ($parts['query'] ?? ''), $query);
        $video_id = (string) ($query['v'] ?? '');
        if (!$video_id && preg_match('#^(?:embed|shorts|live)/([^/]+)#', $path, $match)) {
            $video_id = (string) $match[1];
        }
    } elseif ('vimeo.com' === $host || 'player.vimeo.com' === $host) {
        $provider = 'vimeo';
        if (preg_match('/(?:^|\/)(\d+)(?:$|\/)/', $path, $match)) {
            $video_id = (string) $match[1];
        }
    }

    if ('youtube' === $provider) {
        $video_id = preg_replace('/[^A-Za-z0-9_-]/', '', $video_id);
    } elseif ('vimeo' === $provider) {
        $video_id = preg_replace('/\D+/', '', $video_id);
    }
    if (!$provider || !$video_id) {
        return array();
    }

    $poster_id = (int) tk_product_premium_field('tk_product_video_poster', $post_id);
    $poster_url = $poster_id ? (string) wp_get_attachment_image_url($poster_id, 'full') : '';
    $poster_fallbacks = array();

    if ('youtube' === $provider) {
        $embed_url = 'https://www.youtube-nocookie.com/embed/' . $video_id . '?autoplay=1&rel=0';
        if (!$poster_url) {
            $thumbnail_base = 'https://i.ytimg.com/vi/' . $video_id . '/';
            $poster_url = $thumbnail_base . 'maxresdefault.jpg';
            $poster_fallbacks = array(
                $thumbnail_base . 'maxres1.jpg',
                $thumbnail_base . 'sddefault.jpg',
            );
        }
    } else {
        $embed_url = 'https://player.vimeo.com/video/' . $video_id . '?autoplay=1&dnt=1';
        if (!$poster_url) {
            $poster_url = tk_product_vimeo_thumbnail_url($video_id);
        }
    }

    return array(
        'provider' => $provider,
        'video_id' => $video_id,
        'embed_url' => $embed_url,
        'poster_id' => $poster_id,
        'poster_url' => $poster_url,
        'poster_fallbacks' => array_values(array_filter(array_map('esc_url_raw', $poster_fallbacks))),
    );
}

/**
 * Compatibility wrapper for callers that only need the embed URL.
 */
function tk_product_video_embed_url($post_id)
{
    $video = tk_product_video_data($post_id);
    return (string) ($video['embed_url'] ?? '');
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

    $content = preg_replace_callback(
        '#<(p|figure)(\s[^>]*)?>\s*(<picture\b[^>]*>\s*(?:<source\b[^>]*>\s*)*<img\b[^>]*class="[^"]*tk-product-content-image[^"]*"[^>]*>\s*</picture>)\s*</\1>#is',
        static function ($match) {
            $tag = strtolower((string) $match[1]);
            $opening_tag = '<' . $tag . (string) ($match[2] ?? '') . '>';

            if (class_exists('WP_HTML_Tag_Processor')) {
                $processor = new WP_HTML_Tag_Processor($opening_tag);
                if ($processor->next_tag($tag)) {
                    $processor->add_class('tk-product-content-media--single');
                    $opening_tag = $processor->get_updated_html();
                }
            } else {
                $opening_tag = '<' . $tag . ' class="tk-product-content-media--single">';
            }

            return $opening_tag . $match[3] . '</' . $tag . '>';
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
    $sizes = '(min-width: 1024px) 42vw, calc(100vw - 32px)';
    if (is_singular('san-pham')) {
        $gallery = tk_product_gallery_items(get_queried_object_id());
        $image = $gallery[0] ?? array();
    } elseif (is_post_type_archive('san-pham') || is_tax('danh-muc') || (function_exists('tk_is_product_search') && tk_is_product_search())) {
        global $wp_query;
        $first_post_id = !empty($wp_query->posts[0]) ? (int) $wp_query->posts[0]->ID : 0;
        $image = tk_home_image_data($first_post_id ? get_post_thumbnail_id($first_post_id) : 0);
        $sizes = '(min-width: 1024px) 38vw, calc(100vw - 32px)';
    } else {
        return;
    }
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
        echo ' imagesrcset="' . esc_attr(implode(', ', $srcset)) . '" imagesizes="' . esc_attr($sizes) . '" type="image/avif"';
    }
    echo ' fetchpriority="high">' . "\n";
}
add_action('wp_head', 'tk_product_preload_hero', 3);
