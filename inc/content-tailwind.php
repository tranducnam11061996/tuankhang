<?php
/** Shared Tailwind helpers for public content templates. */
if (!defined('ABSPATH')) exit;

function tk_site_text($vi, $en) { return tk_home_text($vi, $en); }
function tk_site_field($key, $default = '') { return tk_home_field($key, $default); }
function tk_site_logo($white = false) { return tk_home_logo($white); }
function tk_site_menu_tree($menu_id) { return tk_home_menu_tree($menu_id); }
function tk_site_desktop_menu($nodes) { tk_home_desktop_menu($nodes); }
function tk_site_mobile_menu($nodes) { tk_home_mobile_menu($nodes); }

function tk_site_banner($title, $breadcrumbs = array())
{
    $fallback = get_theme_file_uri('/image/background-head-about.png');
    $sources = array('avif' => array(), 'webp' => array());
    foreach (array_keys($sources) as $format) {
        foreach (array(480, 768, 1200, 1702) as $width) {
            $relative = 'assets/dist/images/site/banner-' . $width . '.' . $format;
            if (is_file(get_theme_file_path($relative))) $sources[$format][] = get_theme_file_uri($relative) . ' ' . $width . 'w';
        }
    }
    ?>
    <section class="relative isolate flex min-h-[200px] items-center overflow-hidden bg-slate-500 text-white md:min-h-[250px]" aria-labelledby="site-page-title">
        <picture class="absolute inset-0 -z-20 h-full w-full">
            <?php if ($sources['avif']) : ?><source type="image/avif" srcset="<?php echo esc_attr(implode(', ', $sources['avif'])); ?>" sizes="100vw"><?php endif; ?>
            <?php if ($sources['webp']) : ?><source type="image/webp" srcset="<?php echo esc_attr(implode(', ', $sources['webp'])); ?>" sizes="100vw"><?php endif; ?>
            <img src="<?php echo esc_url($fallback); ?>" width="2000" height="1333" class="h-full w-full object-cover" alt="" loading="eager" decoding="async" fetchpriority="high">
        </picture>
        <div class="absolute inset-0 -z-10 bg-slate-900/45"></div>
        <div class="tk-container py-12 text-center md:py-16">
            <h1 id="site-page-title" class="text-2xl font-bold uppercase leading-tight tracking-wide md:text-[30px]"><?php echo esc_html($title); ?></h1>
            <?php if ($breadcrumbs) : ?>
                <nav class="mt-5 overflow-x-auto pb-1 md:mt-8 md:overflow-visible md:pb-0" aria-label="<?php echo esc_attr(tk_site_text('Đường dẫn trang', 'Breadcrumb')); ?>">
                    <ol class="flex w-max flex-nowrap items-center justify-start gap-x-3 text-sm md:w-auto md:flex-wrap md:justify-center md:gap-y-2 md:text-xl">
                        <?php foreach ($breadcrumbs as $index => $item) : ?><li class="flex items-center gap-3 whitespace-nowrap">
                            <?php if ($index) : ?><svg class="size-4 shrink-0" aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L10.94 10 7.23 6.29a.75.75 0 011.06-1.06l4.24 4.24a.75.75 0 010 1.06l-4.24 4.24a.75.75 0 01-1.08 0z" clip-rule="evenodd"/></svg><?php endif; ?>
                            <?php if (!empty($item['url']) && !is_wp_error($item['url'])) : ?><a class="transition hover:text-blue-100 hover:underline" href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a><?php else : ?><span aria-current="page"><?php echo esc_html($item['label']); ?></span><?php endif; ?>
                        </li><?php endforeach; ?>
                    </ol>
                </nav>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

function tk_site_preload_banner()
{
    if (is_front_page() || is_singular() || is_post_type_archive('san-pham') || is_tax('danh-muc') || (function_exists('tk_is_product_search') && tk_is_product_search()) || is_admin()) return;
    $fallback = get_theme_file_uri('/image/background-head-about.png');
    $srcset = array();
    foreach (array(480, 768, 1200, 1702) as $width) {
        $relative = 'assets/dist/images/site/banner-' . $width . '.avif';
        if (is_file(get_theme_file_path($relative))) $srcset[] = get_theme_file_uri($relative) . ' ' . $width . 'w';
    }
    echo '<link rel="preload" as="image" href="' . esc_url($fallback) . '"';
    if ($srcset) echo ' imagesrcset="' . esc_attr(implode(', ', $srcset)) . '" imagesizes="100vw" type="image/avif"';
    echo ' fetchpriority="high">' . "\n";
}
add_action('wp_head', 'tk_site_preload_banner', 2);

function tk_content_primary_category($post_id = 0)
{
    $terms = get_the_terms($post_id ?: get_the_ID(), 'category');
    if (!$terms || is_wp_error($terms)) return null;
    usort($terms, static fn($a, $b) => count(get_ancestors($b->term_id, 'category', 'taxonomy')) <=> count(get_ancestors($a->term_id, 'category', 'taxonomy')));
    return $terms[0] ?? null;
}

function tk_content_breadcrumbs($post_id = 0)
{
    $items = array(array('label' => tk_site_text('Trang chủ', 'Home'), 'url' => home_url('/')));
    if (is_404()) return array_merge($items, array(array('label' => '404')));
    if (is_search()) return array_merge($items, array(array('label' => sprintf(tk_site_text('Tìm kiếm: %s', 'Search: %s'), get_search_query()))));
    if (is_page()) {
        $post_id = $post_id ?: get_the_ID();
        foreach (array_reverse(get_post_ancestors($post_id)) as $ancestor_id) $items[] = array('label' => get_the_title($ancestor_id), 'url' => get_permalink($ancestor_id));
        $items[] = array('label' => get_the_title($post_id));
        return $items;
    }
    $term = is_category() ? get_queried_object() : tk_content_primary_category($post_id);
    if ($term instanceof WP_Term) {
        foreach (array_reverse(get_ancestors($term->term_id, 'category', 'taxonomy')) as $ancestor_id) {
            $ancestor = get_term($ancestor_id, 'category');
            if ($ancestor instanceof WP_Term) $items[] = array('label' => $ancestor->name, 'url' => get_term_link($ancestor));
        }
        $items[] = array('label' => $term->name, 'url' => is_category() ? '' : get_term_link($term));
    }
    if (is_singular('post')) $items[] = array('label' => get_the_title($post_id ?: get_the_ID()));
    return $items;
}

function tk_content_title()
{
    if (is_404()) return tk_site_text('Không tìm thấy trang', 'Page not found');
    if (is_search()) return sprintf(tk_site_text('Kết quả tìm kiếm cho “%s”', 'Search results for “%s”'), get_search_query());
    if (is_category()) return single_cat_title('', false);
    if (is_tag()) return single_tag_title('', false);
    if (is_tax()) return single_term_title('', false);
    if (is_archive()) return wp_strip_all_tags(get_the_archive_title());
    if (is_singular()) return get_the_title();
    return tk_site_text('Tin tức', 'News');
}

function tk_content_page_group($post_id = 0)
{
    $post_id = $post_id ?: get_queried_object_id();
    foreach (array('company' => array(63, 1340, 1342, 1344), 'services' => array(196, 198), 'policies' => array(71, 73, 76, 78, 502), 'contact' => array(65)) as $group => $ids) {
        if (in_array((int) $post_id, $ids, true)) return $group;
    }
    return 'general';
}

function tk_content_page_links($group)
{
    $groups = array('company' => array(63, 1340, 1342, 1344), 'services' => array(196, 198), 'policies' => array(76, 71, 78, 73, 502), 'contact' => array(63, 1340, 1342, 1344, 65));
    $links = array();
    foreach ($groups[$group] ?? array() as $id) if (get_post_status($id) === 'publish') $links[] = array('label' => get_the_title($id), 'url' => get_permalink($id), 'active' => is_page($id));
    if (!$links) foreach (tk_site_menu_tree(25) as $node) $links[] = array('label' => $node['item']->title, 'url' => $node['item']->url, 'active' => false);
    return $links;
}

function tk_content_group_details($group)
{
    $groups = array(
        'company' => array(
            'label' => tk_site_text('Giới thiệu công ty', 'About the company'),
            'description' => tk_site_text('Khám phá câu chuyện, năng lực và những dấu ấn phát triển của Tuấn Khang.', 'Explore Tuan Khang’s story, capabilities, and development milestones.'),
        ),
        'services' => array(
            'label' => tk_site_text('Dịch vụ chuyên môn', 'Professional services'),
            'description' => tk_site_text('Giải pháp hỗ trợ chuyên môn dành cho bác sĩ, phòng khám và đối tác nha khoa.', 'Professional support solutions for dentists, clinics, and dental partners.'),
        ),
        'policies' => array(
            'label' => tk_site_text('Hỗ trợ khách hàng', 'Customer support'),
            'description' => tk_site_text('Thông tin chính thức giúp quá trình mua hàng và hợp tác rõ ràng, thuận tiện.', 'Official information for a clear and convenient purchasing experience.'),
        ),
        'contact' => array(
            'label' => tk_site_text('Kết nối Tuấn Khang', 'Connect with Tuan Khang'),
            'description' => tk_site_text('Kết nối trực tiếp với đội ngũ Tuấn Khang tại Hà Nội và Thành phố Hồ Chí Minh.', 'Connect directly with the Tuan Khang team in Hanoi and Ho Chi Minh City.'),
        ),
        'news' => array(
            'label' => tk_site_text('Tin tức chuyên môn', 'Professional insights'),
            'description' => tk_site_text('Cập nhật kiến thức, công nghệ và hoạt động mới nhất từ Tuấn Khang.', 'The latest knowledge, technology, and activities from Tuan Khang.'),
        ),
        'general' => array(
            'label' => tk_site_text('Thông tin Tuấn Khang', 'Tuan Khang information'),
            'description' => tk_site_text('Thông tin chính thức từ Công ty TNHH Dược và Thiết bị y tế Tuấn Khang.', 'Official information from Tuan Khang Pharmaceutical and Medical Equipment Co., Ltd.'),
        ),
    );
    return $groups[$group] ?? $groups['general'];
}

function tk_content_prepare_article($html, $title)
{
    $html = is_string($html) ? $html : '';
    $empty_pattern = '#<(p|h[1-6])\b[^>]*>(?:\s|&nbsp;|&\#0*160;|&\#x0*A0;|\x{00A0}|<br\b[^>]*>)*</\1>#iu';
    do {
        $previous = $html;
        $html = preg_replace($empty_pattern, '', $html) ?? $html;
    } while ($html !== $previous);
    $html = preg_replace_callback('#<(p|h[1-6])\b([^>]*)>(.*?)</\1>#isu', static function ($match) {
        if (preg_match('/<(?:img|picture|iframe|video|audio|svg)\b/iu', $match[3])) return $match[0];
        $text = html_entity_decode(wp_strip_all_tags($match[3]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[\s\x{00A0}]+/u', '', $text) ?? $text;
        return $text === '' ? '' : $match[0];
    }, $html) ?? $html;
    $html = preg_replace('#<(?:p|h[1-6])\b[^>]*>\s*$#iu', '', $html) ?? $html;

    $toc = array();
    $used_ids = array();
    $first_heading_checked = false;
    $heading_index = 0;
    $title_key = sanitize_title(wp_strip_all_tags((string) $title));
    $html = preg_replace_callback('#<h([23])\b([^>]*)>(.*?)</h\1>#isu', static function ($match) use (&$toc, &$used_ids, &$first_heading_checked, &$heading_index, $title_key) {
        $level = (int) $match[1];
        $label = html_entity_decode(wp_strip_all_tags($match[3]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $label = trim(preg_replace('/\s+/u', ' ', $label) ?? $label);
        if ($label === '') return '';

        $is_first = !$first_heading_checked;
        $first_heading_checked = true;
        $label_key = sanitize_title($label);
        $duplicates_title = $title_key !== '' && ($label_key === $title_key || strpos($label_key, $title_key . '-') === 0 || strpos($title_key, $label_key . '-') === 0);
        if ($is_first && $duplicates_title) return '';

        $heading_index++;
        $base_id = sanitize_title($label);
        if ($base_id === '') $base_id = 'section-' . $heading_index;
        $base_id = 'section-' . $base_id;
        $id = $base_id;
        $suffix = 2;
        while (isset($used_ids[$id])) $id = $base_id . '-' . $suffix++;
        $used_ids[$id] = true;

        $attributes = preg_replace('/\s+id\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/iu', '', $match[2]) ?? $match[2];
        $toc[] = array('id' => $id, 'level' => $level, 'label' => $label);
        return '<h' . $level . $attributes . ' id="' . esc_attr($id) . '">' . $match[3] . '</h' . $level . '>';
    }, $html) ?? $html;

    $plain = html_entity_decode(wp_strip_all_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $words = preg_split('/\s+/u', trim($plain), -1, PREG_SPLIT_NO_EMPTY);
    $word_count = is_array($words) ? count($words) : 0;

    return array(
        'html' => trim($html),
        'toc' => count($toc) >= 2 ? $toc : array(),
        'word_count' => $word_count,
        'reading_minutes' => max(1, (int) ceil($word_count / 220)),
    );
}

function tk_content_detail_context($post_id, $prepared)
{
    $is_post = get_post_type($post_id) === 'post';
    $group = $is_post ? 'news' : tk_content_page_group($post_id);
    $group_details = tk_content_group_details($group);
    $category = $is_post ? tk_content_primary_category($post_id) : null;
    $description = $group_details['description'];
    if ($is_post) {
        $excerpt = trim(wp_strip_all_tags(get_the_excerpt($post_id)));
        if ($excerpt !== '') $description = wp_trim_words($excerpt, 32, '…');
    }

    $reading_minutes = max(1, (int) ($prepared['reading_minutes'] ?? 1));
    if ($is_post) {
        $meta = array(
            array('label' => tk_site_text('Chuyên mục', 'Category'), 'value' => $category instanceof WP_Term ? $category->name : $group_details['label']),
            array('label' => tk_site_text('Ngày đăng', 'Published'), 'value' => get_the_date(get_option('date_format'), $post_id)),
            array('label' => tk_site_text('Độ dài tài liệu', 'Document length'), 'value' => sprintf(tk_site_text('Đọc khoảng %d phút', 'About %d min read'), $reading_minutes)),
        );
    } else {
        $meta = array(
            array('label' => tk_site_text('Loại tài liệu', 'Document type'), 'value' => $group_details['label']),
            array('label' => tk_site_text('Trạng thái', 'Status'), 'value' => tk_site_text('Thông tin chính thức', 'Official information')),
            array('label' => tk_site_text('Độ dài tài liệu', 'Document length'), 'value' => sprintf(tk_site_text('Đọc khoảng %d phút', 'About %d min read'), $reading_minutes)),
        );
    }

    return array(
        'post_id' => (int) $post_id,
        'title' => get_the_title($post_id),
        'kind' => $is_post ? 'post' : 'page',
        'group' => $group,
        'kicker' => $category instanceof WP_Term ? $category->name : $group_details['label'],
        'description' => $description,
        'breadcrumbs' => tk_content_breadcrumbs($post_id),
        'meta' => $meta,
        'reading_minutes' => $reading_minutes,
    );
}

function tk_content_render_detail_hero($context)
{
    ?>
    <section class="tk-content-hero" aria-labelledby="content-detail-title">
        <div class="tk-container tk-content-hero-grid">
            <div class="tk-content-hero-copy">
                <p class="tk-content-kicker"><span aria-hidden="true"></span><?php echo esc_html($context['kicker']); ?></p>
                <h1 id="content-detail-title"><?php echo esc_html($context['title']); ?></h1>
                <p class="tk-content-dek"><?php echo esc_html($context['description']); ?></p>
                <?php if (!empty($context['breadcrumbs'])) : ?>
                    <nav class="tk-content-breadcrumbs" aria-label="<?php echo esc_attr(tk_site_text('Đường dẫn trang', 'Breadcrumb')); ?>">
                        <ol>
                            <?php foreach ($context['breadcrumbs'] as $index => $item) : ?>
                                <li>
                                    <?php if ($index) : ?><svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L10.94 10 7.23 6.29a.75.75 0 011.06-1.06l4.24 4.24a.75.75 0 010 1.06l-4.24 4.24a.75.75 0 01-1.08 0z" clip-rule="evenodd"/></svg><?php endif; ?>
                                    <?php if (!empty($item['url']) && !is_wp_error($item['url'])) : ?><a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a><?php else : ?><span aria-current="page"><?php echo esc_html($item['label']); ?></span><?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                <?php endif; ?>
            </div>
            <aside class="tk-content-dossier" aria-label="<?php echo esc_attr(tk_site_text('Thông tin tài liệu', 'Document information')); ?>">
                <div class="tk-content-dossier-head"><span><?php echo esc_html(tk_site_text('THÔNG TIN TÓM TẮT', 'SUMMARY')); ?></span><span aria-hidden="true">●</span></div>
                <dl>
                    <?php foreach ($context['meta'] as $index => $item) : ?>
                        <div><dt><?php echo esc_html(sprintf('%02d', $index + 1)); ?> / <?php echo esc_html($item['label']); ?></dt><dd><?php echo esc_html($item['value']); ?></dd></div>
                    <?php endforeach; ?>
                </dl>
            </aside>
        </div>
    </section>
    <?php
}

function tk_content_active_category_ids()
{
    $term = is_category() ? get_queried_object() : (is_singular('post') ? tk_content_primary_category() : null);
    return $term instanceof WP_Term ? array_merge(array((int) $term->term_id), array_map('intval', get_ancestors($term->term_id, 'category', 'taxonomy'))) : array();
}

function tk_content_category_tree()
{
    $children = array();
    foreach (get_categories(array('hide_empty' => true, 'exclude' => array(1))) as $term) $children[(int) $term->parent][] = $term;
    $build = static function ($parent) use (&$build, $children) {
        $nodes = array();
        foreach ($children[$parent] ?? array() as $term) $nodes[] = array('term' => $term, 'children' => $build((int) $term->term_id));
        return $nodes;
    };
    return $build(0);
}

function tk_content_render_category_tree($nodes, $instance, $depth = 0)
{
    if (!$nodes) return;
    $active_ids = tk_content_active_category_ids();
    echo '<ul class="' . ($depth ? 'border-l border-slate-200 pl-3' : 'space-y-1') . '">';
    foreach ($nodes as $node) {
        $term = $node['term']; $active = in_array((int) $term->term_id, $active_ids, true); $panel_id = 'tk-context-' . sanitize_html_class($instance) . '-' . (int) $term->term_id;
        echo '<li class="border-b border-slate-200 last:border-0"><div class="flex min-h-11 items-center gap-1"><a class="flex min-h-11 flex-1 items-center py-2 text-sm transition hover:text-content-accent ' . ($active ? 'font-bold text-primary' : 'text-slate-700') . '" href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
        if ($node['children']) {
            $expanded = $active || $depth === 0;
            echo '<button type="button" data-context-toggle aria-expanded="' . ($expanded ? 'true' : 'false') . '" aria-controls="' . esc_attr($panel_id) . '" class="flex size-11 shrink-0 items-center justify-center text-primary" aria-label="' . esc_attr(tk_site_text('Mở chuyên mục con', 'Toggle subcategory')) . '"><svg class="size-4 transition-transform" aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg></button>';
        }
        echo '</div>';
        if ($node['children']) { echo '<div id="' . esc_attr($panel_id) . '"' . ($expanded ? '' : ' hidden') . ' class="pb-2">'; tk_content_render_category_tree($node['children'], $instance, $depth + 1); echo '</div>'; }
        echo '</li>';
    }
    echo '</ul>';
}

function tk_content_recent_post_ids($limit = 6)
{
    static $cache = array(); $limit = max(1, (int) $limit);
    if (!isset($cache[$limit])) {
        $query = new WP_Query(array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => $limit, 'fields' => 'ids', 'no_found_rows' => true, 'ignore_sticky_posts' => true));
        $cache[$limit] = array_map('intval', $query->posts);
    }
    return $cache[$limit];
}

function tk_content_compact_post($post_id)
{
    $title = get_the_title($post_id); $thumb = get_post_thumbnail_id($post_id);
    echo '<article class="border-b border-slate-200 py-3 last:border-0"><a class="group grid grid-cols-[88px_1fr] items-center gap-3" href="' . esc_url(get_permalink($post_id)) . '"><span class="aspect-[4/3] overflow-hidden rounded-lg border border-slate-200 bg-slate-100">';
    echo $thumb ? tk_picture($thumb, 'post-thumb', array('alt' => $title, 'class' => 'h-full w-full object-cover')) : '<span class="flex h-full items-center justify-center text-xs text-slate-600">No image</span>';
    echo '</span><span><span class="line-clamp-3 font-semibold leading-5 text-slate-800 transition group-hover:text-primary">' . esc_html($title) . '</span><time class="mt-1 block text-xs text-slate-500" datetime="' . esc_attr(get_the_date('c', $post_id)) . '">' . esc_html(get_the_date(get_option('date_format'), $post_id)) . '</time></span></a></article>';
}

function tk_content_detail_render_category_tree($nodes, $instance, $depth = 0)
{
    if (!$nodes) return;
    $active_ids = tk_content_active_category_ids();
    echo '<ul class="tk-reading-nav' . ($depth ? ' is-nested' : '') . '">';
    foreach ($nodes as $node) {
        $term = $node['term'];
        $active = in_array((int) $term->term_id, $active_ids, true);
        $panel_id = 'tk-detail-context-' . sanitize_html_class($instance) . '-' . (int) $term->term_id;
        echo '<li><div class="tk-reading-nav-row"><a class="tk-reading-nav-link' . ($active ? ' is-active' : '') . '"' . ($active ? ' aria-current="page"' : '') . ' href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
        if ($node['children']) {
            $expanded = $active || $depth === 0;
            echo '<button type="button" class="tk-reading-nav-toggle" data-context-toggle aria-expanded="' . ($expanded ? 'true' : 'false') . '" aria-controls="' . esc_attr($panel_id) . '" aria-label="' . esc_attr(sprintf(tk_site_text('Mở chuyên mục %s', 'Toggle %s category'), $term->name)) . '"><svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg></button>';
        }
        echo '</div>';
        if ($node['children']) {
            echo '<div id="' . esc_attr($panel_id) . '"' . ($expanded ? '' : ' hidden') . '>';
            tk_content_detail_render_category_tree($node['children'], $instance, $depth + 1);
            echo '</div>';
        }
        echo '</li>';
    }
    echo '</ul>';
}

function tk_content_detail_compact_post($post_id)
{
    $title = get_the_title($post_id);
    $thumb = get_post_thumbnail_id($post_id);
    echo '<article class="tk-reading-recent"><a href="' . esc_url(get_permalink($post_id)) . '">';
    if ($thumb) echo '<span class="tk-reading-recent-image">' . tk_picture($thumb, 'post-thumb', array('alt' => $title, 'class' => 'h-full w-full object-cover')) . '</span>';
    echo '<span class="tk-reading-recent-copy"><span>' . esc_html($title) . '</span><time datetime="' . esc_attr(get_the_date('c', $post_id)) . '">' . esc_html(get_the_date(get_option('date_format'), $post_id)) . '</time></span></a></article>';
}

function tk_content_render_toc($toc_items, $instance)
{
    if (count($toc_items) < 2) return;
    echo '<nav class="tk-reading-toc" data-content-toc aria-label="' . esc_attr(tk_site_text('Mục lục bài viết', 'Article table of contents')) . '"><ol>';
    foreach ($toc_items as $item) {
        echo '<li class="' . ((int) $item['level'] === 3 ? 'is-nested' : '') . '"><a data-toc-link href="#' . esc_attr($item['id']) . '">' . esc_html($item['label']) . '</a></li>';
    }
    echo '</ol></nav>';
}

function tk_content_detail_sidebar($instance, $toc_items)
{
    $is_news = is_singular('post');
    $group = $is_news ? 'news' : tk_content_page_group();
    $headings = array(
        'news' => tk_site_text('Chuyên mục', 'Categories'),
        'company' => tk_site_text('Giới thiệu công ty', 'About the company'),
        'services' => tk_site_text('Dịch vụ', 'Services'),
        'policies' => tk_site_text('Hỗ trợ khách hàng', 'Customer support'),
        'contact' => tk_site_text('Liên hệ', 'Contact'),
        'general' => tk_site_text('Khám phá', 'Explore'),
    );
    $section_number = 1;
    ?>
    <div class="tk-reading-rail" data-reading-rail>
        <div class="tk-reading-rail-head"><span>TUAN KHANG</span><span><?php echo esc_html(date('Y')); ?></span></div>
        <section class="tk-reading-section" aria-labelledby="<?php echo esc_attr($instance); ?>-detail-context-title">
            <h2 id="<?php echo esc_attr($instance); ?>-detail-context-title"><span><?php echo esc_html(sprintf('%02d', $section_number++)); ?></span><?php echo esc_html($headings[$group] ?? $headings['general']); ?></h2>
            <?php if ($is_news) : ?>
                <?php tk_content_detail_render_category_tree(tk_content_category_tree(), $instance); ?>
            <?php else : ?>
                <ul class="tk-reading-nav">
                    <?php foreach (tk_content_page_links($group) as $link) : ?><li><div class="tk-reading-nav-row"><a class="tk-reading-nav-link<?php echo $link['active'] ? ' is-active' : ''; ?>"<?php echo $link['active'] ? ' aria-current="page"' : ''; ?> href="<?php echo esc_url($link['url']); ?>"><?php echo esc_html($link['label']); ?></a></div></li><?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
        <?php if (count($toc_items) >= 2) : ?>
            <section class="tk-reading-section" aria-labelledby="<?php echo esc_attr($instance); ?>-detail-toc-title">
                <h2 id="<?php echo esc_attr($instance); ?>-detail-toc-title"><span><?php echo esc_html(sprintf('%02d', $section_number++)); ?></span><?php echo esc_html(tk_site_text('Trong bài viết', 'In this article')); ?></h2>
                <?php tk_content_render_toc($toc_items, $instance); ?>
            </section>
        <?php endif; ?>
        <?php if ($group !== 'contact') : ?>
            <section class="tk-reading-section" aria-labelledby="<?php echo esc_attr($instance); ?>-detail-recent-title">
                <h2 id="<?php echo esc_attr($instance); ?>-detail-recent-title"><span><?php echo esc_html(sprintf('%02d', $section_number)); ?></span><?php echo esc_html(tk_site_text('Bài viết mới', 'Recent posts')); ?></h2>
                <div class="tk-reading-recent-list"><?php foreach (tk_content_recent_post_ids(4) as $recent_id) tk_content_detail_compact_post($recent_id); ?></div>
            </section>
        <?php endif; ?>
    </div>
    <?php
}

function tk_content_sidebar($instance = 'desktop', $toc_items = array(), $detail = false)
{
    if ($detail) {
        tk_content_detail_sidebar($instance, is_array($toc_items) ? $toc_items : array());
        return;
    }
    $is_news = is_home() || is_archive() || is_singular('post') || (is_search() && !tk_is_product_search());
    $group = $is_news ? 'news' : tk_content_page_group();
    $headings = array('news' => tk_site_text('Chuyên mục', 'Categories'), 'company' => tk_site_text('Giới thiệu công ty', 'About the company'), 'services' => tk_site_text('Dịch vụ', 'Services'), 'policies' => tk_site_text('Hỗ trợ khách hàng', 'Customer support'), 'contact' => tk_site_text('Liên hệ', 'Contact'), 'general' => tk_site_text('Khám phá', 'Explore'));
    ?>
    <div>
        <section aria-labelledby="<?php echo esc_attr($instance); ?>-context-title">
            <h2 id="<?php echo esc_attr($instance); ?>-context-title" class="bg-primary px-4 py-3 text-base font-bold uppercase text-white"><?php echo esc_html($headings[$group]); ?></h2>
            <div class="bg-slate-50 px-4 py-2">
                <?php if ($group === 'news') : ?>
                    <?php tk_content_render_category_tree(tk_content_category_tree(), $instance); ?>
                <?php else : ?>
                    <ul class="space-y-1"><?php foreach (tk_content_page_links($group) as $link) : ?><li class="border-b border-slate-200 last:border-0"><a class="flex min-h-11 items-center py-2 text-sm transition hover:text-content-accent <?php echo $link['active'] ? 'font-bold text-primary' : 'text-slate-700'; ?>" href="<?php echo esc_url($link['url']); ?>"><?php echo esc_html($link['label']); ?></a></li><?php endforeach; ?></ul>
                <?php endif; ?>
            </div>
        </section>
        <?php if ($group === 'contact') : ?>
            <section class="mt-6 rounded-xl bg-slate-50 p-4" aria-labelledby="<?php echo esc_attr($instance); ?>-quick-contact-title"><h2 id="<?php echo esc_attr($instance); ?>-quick-contact-title" class="font-bold uppercase text-primary"><?php echo esc_html(tk_site_text('Liên hệ nhanh', 'Quick contact')); ?></h2><p class="mt-3"><a class="font-bold text-content-accent hover:underline" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', (string) tk_site_field('wpcf-so-hotline'))); ?>"><?php echo esc_html((string) tk_site_field('wpcf-so-hotline')); ?></a></p><p class="mt-2 break-all"><a class="text-primary hover:underline" href="mailto:<?php echo esc_attr((string) tk_site_field('wpcf-email')); ?>"><?php echo esc_html((string) tk_site_field('wpcf-email')); ?></a></p></section>
        <?php else : ?>
            <section class="mt-6" aria-labelledby="<?php echo esc_attr($instance); ?>-recent-title"><h2 id="<?php echo esc_attr($instance); ?>-recent-title" class="bg-primary px-4 py-3 text-base font-bold uppercase text-white"><?php echo esc_html(tk_site_text('Bài viết mới', 'Recent posts')); ?></h2><div class="px-1"><?php foreach (tk_content_recent_post_ids() as $recent_id) tk_content_compact_post($recent_id); ?></div></section>
        <?php endif; ?>
    </div>
    <?php
}

function tk_content_sidebar_button($toc_items = array(), $detail = false)
{
    if ($detail) {
        $label = count($toc_items) >= 2 ? tk_site_text('Mục lục & điều hướng', 'Contents & navigation') : tk_site_text('Điều hướng nội dung', 'Content navigation');
        echo '<button type="button" data-context-filter-open aria-controls="context-filter-drawer" aria-expanded="false" class="tk-reading-drawer-button"><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16M4 12h10M4 18h16"/><path d="M18 10l2 2-2 2"/></svg><span>' . esc_html($label) . '</span><span aria-hidden="true">→</span></button>';
        return;
    }
    echo '<button type="button" data-context-filter-open aria-controls="context-filter-drawer" aria-expanded="false" class="mb-6 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 font-bold uppercase text-white lg:hidden"><svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M7 12h10M10 18h4"/></svg>' . esc_html(tk_site_text('Điều hướng nội dung', 'Content navigation')) . '</button>';
}

function tk_content_sidebar_drawer($toc_items = array(), $detail = false)
{
    if ($detail) {
        ?>
        <div data-context-filter-overlay class="tk-context-filter-overlay tk-reading-drawer-overlay"></div>
        <aside id="context-filter-drawer" data-context-filter-drawer aria-hidden="true" inert class="tk-context-filter-drawer tk-reading-drawer" aria-label="<?php echo esc_attr(tk_site_text('Mục lục và điều hướng nội dung', 'Contents and navigation')); ?>">
            <div class="tk-reading-drawer-head"><div><span>TK / CONTENT</span><h2><?php echo esc_html(tk_site_text('Điều hướng', 'Navigation')); ?></h2></div><button type="button" data-context-filter-close aria-label="<?php echo esc_attr(tk_site_text('Đóng điều hướng', 'Close navigation')); ?>"><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 6 6 18M6 6l12 12"/></svg></button></div>
            <?php tk_content_sidebar('mobile', $toc_items, true); ?>
        </aside>
        <?php
        return;
    }
    ?>
    <div data-context-filter-overlay class="tk-context-filter-overlay fixed inset-0 z-50 bg-slate-950/55 lg:hidden"></div>
    <aside id="context-filter-drawer" data-context-filter-drawer aria-hidden="true" inert class="tk-context-filter-drawer fixed inset-y-0 left-0 z-[60] w-[min(90vw,380px)] overflow-y-auto bg-white p-5 shadow-2xl lg:hidden" aria-label="<?php echo esc_attr(tk_site_text('Điều hướng nội dung', 'Content navigation')); ?>">
        <div class="mb-5 flex items-center justify-between border-b border-slate-200 pb-4"><h2 class="text-lg font-bold uppercase text-primary"><?php echo esc_html(tk_site_text('Điều hướng', 'Navigation')); ?></h2><button type="button" data-context-filter-close class="flex size-11 items-center justify-center rounded-lg text-primary" aria-label="<?php echo esc_attr(tk_site_text('Đóng điều hướng', 'Close navigation')); ?>"><svg class="size-7" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg></button></div>
        <?php tk_content_sidebar('mobile'); ?>
    </aside>
    <?php
}

function tk_content_render_endcap($context)
{
    $is_post = ($context['kind'] ?? '') === 'post';
    $group = $context['group'] ?? 'general';
    if (!$is_post && !in_array($group, array('policies', 'services'), true)) return;

    $hotline = (string) tk_site_field('wpcf-so-hotline');
    $email = (string) tk_site_field('wpcf-email');
    $title = $is_post ? tk_site_text('Trao đổi cùng đội ngũ chuyên môn', 'Talk with our professional team') : tk_site_text('Bạn cần hỗ trợ thêm?', 'Need more support?');
    $description = $is_post
        ? tk_site_text('Tuấn Khang sẵn sàng đồng hành cùng bác sĩ và phòng khám trong quá trình lựa chọn giải pháp phù hợp.', 'Tuan Khang supports dentists and clinics in selecting the right solutions.')
        : tk_site_text('Đội ngũ Tuấn Khang sẵn sàng giải đáp thông tin mua hàng, giao nhận và chính sách dịch vụ.', 'The Tuan Khang team can help with purchasing, delivery, and service policies.');
    ?>
    <section class="tk-content-endcap" aria-labelledby="tk-content-endcap-title">
        <div class="tk-content-endcap-copy">
            <p><?php echo esc_html($is_post ? tk_site_text('ĐỒNG HÀNH CHUYÊN MÔN', 'PROFESSIONAL PARTNERSHIP') : tk_site_text('HỖ TRỢ TRỰC TIẾP', 'DIRECT SUPPORT')); ?></p>
            <h2 id="tk-content-endcap-title"><?php echo esc_html($title); ?></h2>
            <span><?php echo esc_html($description); ?></span>
        </div>
        <div class="tk-content-endcap-actions">
            <a class="tk-content-endcap-primary" href="<?php echo esc_url(home_url('/lien-he/')); ?>"><span><?php echo esc_html(tk_site_text('Liên hệ Tuấn Khang', 'Contact Tuan Khang')); ?></span><span aria-hidden="true">→</span></a>
            <?php if ($hotline) : ?><a class="tk-content-endcap-secondary" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $hotline)); ?>"><small>Hotline</small><strong><?php echo esc_html($hotline); ?></strong></a><?php elseif ($email) : ?><a class="tk-content-endcap-secondary" href="mailto:<?php echo esc_attr($email); ?>"><small>Email</small><strong><?php echo esc_html($email); ?></strong></a><?php endif; ?>
        </div>
    </section>
    <?php
}

function tk_content_post_card($post_id, $related = false, $priority = false)
{
    $title = get_the_title($post_id); $url = get_permalink($post_id); $thumb = get_post_thumbnail_id($post_id);
    $image_args = array('alt' => $title, 'class' => 'h-full w-full object-cover transition duration-300 hover:scale-[1.02]');
    if ($priority) {
        $image_args['loading'] = 'eager';
        $image_args['fetchpriority'] = 'high';
    }
    if ($related) {
        echo '<article class="tk-card group flex h-full flex-col"><a class="tk-card-image" href="' . esc_url($url) . '">' . ($thumb ? tk_picture($thumb, 'post-card', array('alt' => $title, 'class' => 'h-full w-full object-cover')) : '<span class="flex h-full items-center justify-center text-sm text-slate-600">No image</span>') . '</a><div class="flex flex-1 flex-col p-5"><h3 class="line-clamp-3 text-lg font-bold leading-6 text-primary"><a class="hover:text-content-accent" href="' . esc_url($url) . '">' . esc_html($title) . '</a></h3><p class="mt-3 line-clamp-3 text-sm text-slate-600">' . esc_html(wp_trim_words(get_the_excerpt($post_id), 24, '…')) . '</p></div></article>';
        return;
    }
    echo '<article class="border-b border-slate-200 py-6 first:pt-0"><div class="grid gap-5 sm:grid-cols-[minmax(220px,40%)_1fr] sm:items-start"><a class="aspect-[16/10] overflow-hidden border border-slate-200 bg-slate-100" href="' . esc_url($url) . '">' . ($thumb ? tk_picture($thumb, 'post-card', $image_args) : '<span class="flex h-full items-center justify-center text-sm text-slate-600">No image</span>') . '</a><div><h2 class="text-lg font-bold uppercase leading-7 text-primary md:text-xl"><a class="hover:text-content-accent" href="' . esc_url($url) . '">' . esc_html($title) . '</a></h2><p class="mt-3 leading-7 text-slate-700">' . esc_html(wp_trim_words(get_the_excerpt($post_id), 50, '…')) . '</p><a class="mt-4 inline-flex min-h-11 items-center font-bold text-content-button hover:text-content-accent" href="' . esc_url($url) . '">' . esc_html(tk_site_text('Xem chi tiết', 'Read more')) . '<span class="ml-2" aria-hidden="true">→</span></a></div></div></article>';
}

function tk_content_pagination()
{
    global $wp_query;
    if ((int) $wp_query->max_num_pages <= 1) return;
    $links = paginate_links(array('total' => (int) $wp_query->max_num_pages, 'current' => max(1, (int) get_query_var('paged')), 'type' => 'array', 'prev_text' => tk_site_text('Trước', 'Previous'), 'next_text' => tk_site_text('Sau', 'Next')));
    if (!$links) return;
    echo '<nav class="mt-10" aria-label="' . esc_attr(tk_site_text('Phân trang', 'Pagination')) . '"><ul class="flex flex-wrap items-center justify-center gap-2">';
    foreach ($links as $link) echo '<li class="tk-page-link">' . wp_kses_post($link) . '</li>';
    echo '</ul></nav>';
}

function tk_content_related_post_ids($post_id, $limit = 4)
{
    $ids = array(); $term = tk_content_primary_category($post_id);
    if ($term instanceof WP_Term) {
        $query = new WP_Query(array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => $limit, 'post__not_in' => array($post_id), 'fields' => 'ids', 'no_found_rows' => true, 'ignore_sticky_posts' => true, 'category__in' => array((int) $term->term_id)));
        $ids = array_map('intval', $query->posts);
    }
    if (count($ids) < $limit) {
        $fallback = new WP_Query(array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => $limit - count($ids), 'post__not_in' => array_merge(array($post_id), $ids), 'fields' => 'ids', 'no_found_rows' => true, 'ignore_sticky_posts' => true));
        $ids = array_merge($ids, array_map('intval', $fallback->posts));
    }
    return array_slice(array_values(array_unique($ids)), 0, $limit);
}

function tk_content_localize_images($content)
{
    if (is_admin() || !is_string($content) || strpos($content, 'wp-image-') === false || !class_exists('WP_HTML_Tag_Processor')) return $content;
    $processor = new WP_HTML_Tag_Processor($content);
    while ($processor->next_tag('img')) {
        if (!preg_match('/(?:^|\s)wp-image-(\d+)(?:\s|$)/', (string) $processor->get_attribute('class'), $match)) continue;
        $attachment_id = (int) $match[1]; $image = wp_get_attachment_image_src($attachment_id, 'large');
        if (!$image) $image = wp_get_attachment_image_src($attachment_id, 'full');
        if (!$image) continue;
        $processor->set_attribute('src', $image[0]); $processor->set_attribute('width', (string) $image[1]); $processor->set_attribute('height', (string) $image[2]);
        $processor->set_attribute('loading', 'lazy'); $processor->set_attribute('decoding', 'async'); $processor->set_attribute('sizes', '(min-width: 1024px) 850px, calc(100vw - 32px)');
        $srcset = wp_get_attachment_image_srcset($attachment_id, 'large'); if ($srcset) $processor->set_attribute('srcset', $srcset);
    }
    $content = $processor->get_updated_html();
    if (is_page() && !is_front_page()) {
        $content = preg_replace_callback('/<img\b[^>]*class="[^"]*wp-image-(\d+)[^"]*"[^>]*>/i', static function ($match) {
            $id = (int) $match[1]; if (!wp_get_attachment_url($id)) return $match[0];
            return tk_picture($id, 'page-content', array('alt' => (string) get_post_meta($id, '_wp_attachment_image_alt', true), 'class' => 'tk-content-image'));
        }, $content);
    }
    return $content;
}
add_filter('the_content', 'tk_content_localize_images', 20);
