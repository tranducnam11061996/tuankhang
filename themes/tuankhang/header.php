<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('tk-tailwind'); ?>>
<?php wp_body_open(); ?>
<?php
$site_menu = tk_site_menu_tree(25);
$hotline = (string) tk_site_field('wpcf-so-hotline');
$email = (string) tk_site_field('wpcf-email');
$company_name = tk_site_text('Công ty TNHH Dược và Thiết bị y tế Tuấn Khang', 'Tuan Khang Pharmaceutical and Medical Equipment Co., Ltd.');
$consultation_url = home_url('/lien-he/');
?>
<a href="#main-content" class="sr-only z-[100] rounded bg-white px-4 py-3 text-primary focus:not-sr-only focus:fixed focus:left-3 focus:top-3">
    <?php echo esc_html(tk_site_text('Bỏ qua đến nội dung', 'Skip to content')); ?>
</a>

<div class="tk-topbar hidden bg-tech text-blue-50 lg:block">
    <div class="tk-container flex min-h-10 items-center justify-between gap-5 text-[13px]">
        <div class="flex items-center gap-6">
            <?php if ($hotline) : ?>
                <a class="tk-topbar-link" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $hotline)); ?>">
                    <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.79 19.79 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.69 2.8a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0122 16.92z"/></svg>
                    <span><?php echo esc_html(tk_site_text('Tư vấn', 'Consultation')); ?>: <?php echo esc_html($hotline); ?></span>
                </a>
            <?php endif; ?>
            <?php if ($email) : ?>
                <a class="tk-topbar-link" href="mailto:<?php echo esc_attr($email); ?>">
                    <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="m22 6-10 7L2 6"/></svg>
                    <span><?php echo esc_html($email); ?></span>
                </a>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-1">
            <div data-dropdown data-open="false" class="relative">
                <button type="button" data-dropdown-trigger aria-expanded="false" class="tk-topbar-control">
                    <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <?php echo esc_html(tk_site_text('Tìm kiếm', 'Search')); ?>
                </button>
                <div class="tk-dropdown-panel w-80"><?php get_search_form(); ?></div>
            </div>
        </div>
    </div>
</div>

<div data-header-sentinel class="h-px" aria-hidden="true"></div>
<header id="site-header" data-site-header data-scrolled="false" class="tk-site-header sticky top-0 z-40 bg-white">
    <div class="tk-desktop-header tk-container hidden min-h-20 items-center gap-4 lg:flex xl:gap-6">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="flex shrink-0 items-center" aria-label="<?php echo esc_attr($company_name); ?>">
            <img src="<?php echo esc_url(tk_site_logo()); ?>" width="200" height="139" class="h-16 w-auto xl:h-[68px]" alt="<?php echo esc_attr($company_name); ?>">
        </a>
        <nav aria-label="<?php echo esc_attr(tk_site_text('Điều hướng chính', 'Primary navigation')); ?>" class="ml-auto min-w-0"><?php tk_site_desktop_menu($site_menu); ?></nav>
        <a class="tk-header-consultation" href="<?php echo esc_url($consultation_url); ?>" aria-label="<?php echo esc_attr(tk_site_text('Nhận tư vấn', 'Get consultation')); ?>">
            <svg class="size-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/><path d="M8 9h8M8 13h5"/></svg>
            <span class="hidden 2xl:inline"><?php echo esc_html(tk_site_text('Nhận tư vấn', 'Get consultation')); ?></span>
        </a>
    </div>

    <div class="tk-mobile-header tk-container flex min-h-[72px] items-center justify-between lg:hidden">
        <button type="button" data-menu-open aria-controls="site-mobile-menu" aria-expanded="false" class="tk-icon-button" aria-label="<?php echo esc_attr(tk_site_text('Mở menu', 'Open menu')); ?>">
            <svg class="size-7" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($company_name); ?>"><img src="<?php echo esc_url(tk_site_logo()); ?>" width="200" height="139" class="h-[54px] w-auto" alt="<?php echo esc_attr($company_name); ?>"></a>
        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $hotline)); ?>" class="tk-icon-button" aria-label="<?php echo esc_attr(tk_site_text('Gọi hotline', 'Call hotline')); ?>">
            <svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.79 19.79 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.69 2.8a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0122 16.92z"/></svg>
        </a>
    </div>
</header>

<div data-menu-overlay class="tk-overlay fixed inset-0 z-50 bg-tech/70 lg:hidden"></div>
<aside id="site-mobile-menu" data-menu-drawer aria-hidden="true" inert class="tk-drawer fixed inset-y-0 left-0 z-[60] w-[min(88vw,360px)] overflow-y-auto bg-white p-5 shadow-2xl lg:hidden">
    <div class="mb-5 flex items-center justify-between border-b border-line pb-4">
        <img src="<?php echo esc_url(tk_site_logo()); ?>" width="200" height="139" class="h-14 w-auto" alt="<?php echo esc_attr($company_name); ?>">
        <button type="button" data-menu-close class="tk-icon-button" aria-label="<?php echo esc_attr(tk_site_text('Đóng menu', 'Close menu')); ?>"><svg class="size-7" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
    </div>
    <div class="mb-5"><?php get_search_form(array('aria_label' => tk_site_text('Tìm kiếm sản phẩm', 'Search products'))); ?></div>
    <nav aria-label="<?php echo esc_attr(tk_site_text('Menu di động', 'Mobile navigation')); ?>"><?php tk_site_mobile_menu($site_menu); ?></nav>
    <a class="tk-cta tk-cta-primary mt-6 w-full" href="<?php echo esc_url($consultation_url); ?>"><span><?php echo esc_html(tk_site_text('Nhận tư vấn chuyên môn', 'Get expert consultation')); ?></span><svg class="tk-cta-arrow size-4" aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h12M11 5l5 5-5 5"/></svg></a>
</aside>
