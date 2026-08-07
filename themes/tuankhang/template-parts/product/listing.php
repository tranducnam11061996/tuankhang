<?php
if (!defined('ABSPATH')) {
    exit;
}

$title = tk_product_listing_title();
$context = tk_product_listing_context();
$breadcrumbs = tk_product_breadcrumbs();
$hero_product_id = (int) $context['hero_product_id'];
$hero_thumbnail_id = $hero_product_id ? get_post_thumbnail_id($hero_product_id) : 0;
$hero_term = $hero_product_id ? tk_product_primary_term($hero_product_id) : null;
$sort = tk_product_current_sort();
$search_query = get_search_query();
$top_category_count = count(tk_home_menu_tree(24));
$clear_url = is_tax('danh-muc') ? tk_product_listing_base_url() : (get_post_type_archive_link('san-pham') ?: home_url('/san-pham/'));
$is_category_listing = is_tax('danh-muc');
$hotline = $is_category_listing ? (string) tk_site_option('contact.hotline') : '';
$dialog_style_url = $is_category_listing ? tk_product_dialog_style_url() : '';
$sort_options = array(
    'default' => tk_home_text('Sắp xếp mặc định', 'Default order'),
    'title_asc' => tk_home_text('Tên: A–Z', 'Name: A–Z'),
    'title_desc' => tk_home_text('Tên: Z–A', 'Name: Z–A'),
    'newest' => tk_home_text('Mới nhất', 'Newest'),
);

get_header();
?>
<main id="main-content" class="tk-product-listing" data-product-catalog<?php if ($dialog_style_url) : ?> data-product-dialog-style="<?php echo esc_url($dialog_style_url); ?>"<?php endif; ?>>
    <section class="tk-catalog-hero">
        <div class="tk-catalog-grid-pattern" aria-hidden="true"></div>
        <div class="tk-container tk-catalog-hero-grid">
            <div class="tk-catalog-hero-copy">
                <nav class="tk-catalog-breadcrumbs" aria-label="<?php echo esc_attr(tk_home_text('Đường dẫn trang', 'Breadcrumb')); ?>">
                    <ol>
                        <?php foreach ($breadcrumbs as $index => $item) : ?>
                            <li>
                                <?php if (!empty($item['url'])) : ?><a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a><?php else : ?><span aria-current="page"><?php echo esc_html($item['label']); ?></span><?php endif; ?>
                                <?php if ($index < count($breadcrumbs) - 1) : ?><svg aria-hidden="true" viewBox="0 0 16 16" fill="none" stroke="currentColor"><path d="m6 3 5 5-5 5"/></svg><?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
                <p class="tk-catalog-hero-eyebrow"><span></span><?php echo esc_html($context['eyebrow']); ?></p>
                <h1><?php echo esc_html($context['title']); ?></h1>
                <p class="tk-catalog-hero-description"><?php echo esc_html($context['description']); ?></p>
                <div class="tk-catalog-hero-actions">
                    <a class="tk-catalog-button tk-catalog-button--primary" href="#product-catalog"><?php echo esc_html(tk_home_text('Khám phá danh mục', 'Explore catalogue')); ?><svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h12M11 5l5 5-5 5"/></svg></a>
                    <?php if ($is_category_listing) : ?>
                        <div class="tk-catalog-consultation-actions">
                            <?php get_template_part('template-parts/product/consultation-actions', null, array(
                                'label' => tk_home_text('Tư vấn giải pháp', 'Solution consultation'),
                                'source' => 'catalog-hero',
                                'hotline' => $hotline,
                            )); ?>
                        </div>
                    <?php else : ?>
                        <a class="tk-catalog-button tk-catalog-button--secondary" href="<?php echo esc_url((string) tk_site_option('contact.consultation_url', home_url('/lien-he/'))); ?>"><?php echo esc_html(tk_home_text('Tư vấn giải pháp', 'Solution consultation')); ?></a>
                    <?php endif; ?>
                </div>
                <dl class="tk-catalog-hero-stats">
                    <div><dt><?php echo esc_html(number_format_i18n((int) $context['found'])); ?></dt><dd><?php echo esc_html(tk_home_text('Sản phẩm chuyên môn', 'Professional products')); ?></dd></div>
                    <div><dt><?php echo esc_html(number_format_i18n($top_category_count)); ?></dt><dd><?php echo esc_html(tk_home_text('Nhóm giải pháp', 'Solution groups')); ?></dd></div>
                    <div><dt>24 / 7</dt><dd><?php echo esc_html(tk_home_text('Hỗ trợ chuyên môn', 'Specialist support')); ?></dd></div>
                </dl>
            </div>

            <div class="tk-catalog-hero-visual" aria-label="<?php echo esc_attr($hero_product_id ? get_the_title($hero_product_id) : tk_home_text('Giải pháp nha khoa Tuấn Khang', 'Tuan Khang dental solutions')); ?>">
                <span class="tk-catalog-hero-visual-code" aria-hidden="true">TK / DENTAL SYSTEMS</span>
                <span class="tk-catalog-hero-orbit" aria-hidden="true"></span>
                <div class="tk-catalog-hero-stage">
                    <?php if ($hero_thumbnail_id) : ?>
                        <?php echo tk_picture($hero_thumbnail_id, 'product-listing-hero', array('alt' => get_the_title($hero_product_id), 'class' => 'tk-catalog-hero-image', 'loading' => 'eager', 'fetchpriority' => 'high')); ?>
                    <?php else : ?>
                        <span class="tk-catalog-hero-fallback" aria-hidden="true"><?php echo tk_product_category_icon(''); ?><i></i></span>
                    <?php endif; ?>
                </div>
                <?php if ($hero_product_id) : ?>
                    <a class="tk-catalog-hero-product" href="<?php echo esc_url(get_permalink($hero_product_id)); ?>">
                        <span><?php echo esc_html($hero_term instanceof WP_Term ? $hero_term->name : tk_home_text('Sản phẩm tiêu biểu', 'Representative product')); ?></span>
                        <strong><?php echo esc_html(get_the_title($hero_product_id)); ?></strong>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h12M11 5l5 5-5 5"/></svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="product-catalog" class="tk-catalog-section" aria-labelledby="tk-catalog-results-title">
        <div class="tk-container">
            <div class="tk-catalog-toolbar" data-reveal>
                <div class="tk-catalog-toolbar-heading">
                    <p><?php echo esc_html(tk_home_text('Hệ sinh thái sản phẩm', 'Product ecosystem')); ?></p>
                    <h2 id="tk-catalog-results-title"><?php echo esc_html($title); ?></h2>
                    <span><?php echo esc_html(sprintf(tk_home_text('%s sản phẩm', '%s results'), number_format_i18n((int) $context['found']))); ?></span>
                </div>
                <form class="tk-catalog-search" action="<?php echo esc_url(tk_product_listing_base_url()); ?>" method="get" role="search">
                    <input type="hidden" name="post_type" value="san-pham">
                    <label class="tk-catalog-search-field">
                        <span class="screen-reader-text"><?php echo esc_html(tk_home_text('Tìm theo tên sản phẩm', 'Search by product name')); ?></span>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="9" r="5.5"/><path d="m13 13 4 4"/></svg>
                        <input type="search" name="s" value="<?php echo esc_attr($search_query); ?>" placeholder="<?php echo esc_attr(tk_home_text('Tìm theo tên sản phẩm…', 'Search product name…')); ?>">
                    </label>
                    <label class="tk-catalog-sort-field">
                        <span class="screen-reader-text"><?php echo esc_html(tk_home_text('Sắp xếp sản phẩm', 'Sort products')); ?></span>
                        <select name="tk_product_sort" data-product-sort>
                            <?php foreach ($sort_options as $value => $label) : ?><option value="<?php echo esc_attr($value); ?>" <?php selected($sort, $value); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?>
                        </select>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m6 8 4 4 4-4"/></svg>
                    </label>
                    <button type="submit" class="tk-catalog-search-submit"><?php echo esc_html(tk_home_text('Tìm kiếm', 'Search')); ?></button>
                </form>
                <button type="button" data-product-filter-open aria-controls="product-filter-drawer" aria-expanded="false" class="tk-catalog-filter-open">
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                    <?php echo esc_html(tk_home_text('Mở danh mục sản phẩm', 'Open product categories')); ?>
                </button>
            </div>

            <div class="tk-catalog-layout">
                <aside class="tk-catalog-sidebar" aria-label="<?php echo esc_attr(tk_home_text('Điều hướng sản phẩm', 'Product navigation')); ?>">
                    <?php tk_product_sidebar('desktop'); ?>
                </aside>

                <div class="tk-catalog-results">
                    <?php if (have_posts()) : ?>
                        <div class="tk-catalog-product-grid">
                            <?php while (have_posts()) : the_post(); ?>
                                <?php tk_product_listing_card(get_the_ID()); ?>
                            <?php endwhile; ?>
                        </div>
                        <?php tk_product_pagination(); ?>
                    <?php else : ?>
                        <div class="tk-catalog-empty" data-reveal>
                            <span aria-hidden="true"><?php echo tk_product_category_icon(''); ?></span>
                            <p><?php echo esc_html(tk_home_text('Không có tín hiệu phù hợp', 'No matching signal')); ?></p>
                            <h3><?php echo esc_html(tk_home_text('Chưa tìm thấy sản phẩm bạn cần', 'We could not find the product you need')); ?></h3>
                            <div><a class="tk-catalog-button tk-catalog-button--primary" href="<?php echo esc_url($clear_url); ?>"><?php echo esc_html(tk_home_text('Xóa bộ lọc', 'Clear filters')); ?></a><a class="tk-catalog-button tk-catalog-button--secondary" href="<?php echo esc_url(get_post_type_archive_link('san-pham')); ?>"><?php echo esc_html(tk_home_text('Xem toàn bộ sản phẩm', 'View all products')); ?></a></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php tk_product_featured_strip(); ?>
</main>

<div data-product-filter-overlay class="tk-product-filter-overlay" hidden></div>
<aside id="product-filter-drawer" data-product-filter-drawer aria-hidden="true" inert class="tk-product-filter-drawer" aria-label="<?php echo esc_attr(tk_home_text('Danh mục sản phẩm', 'Product categories')); ?>">
    <div class="tk-product-filter-head">
        <div><span>TK / CATALOG</span><h2><?php echo esc_html(tk_home_text('Duyệt danh mục', 'Browse categories')); ?></h2></div>
        <button type="button" data-product-filter-close aria-label="<?php echo esc_attr(tk_home_text('Đóng danh mục', 'Close categories')); ?>"><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
    </div>
    <?php tk_product_sidebar('mobile'); ?>
</aside>

<?php if ($is_category_listing) : ?>
    <?php get_template_part('template-parts/product/consultation-dialog'); ?>
<?php endif; ?>

<?php get_footer(); ?>
