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
$home_menu = tk_home_menu_tree(25);
$hotline = (string) tk_home_field('wpcf-so-hotline');
$email = (string) tk_home_field('wpcf-email');
$company_name = tk_home_text('Công ty TNHH Dược và Thiết bị y tế Tuấn Khang', 'Tuan Khang Pharmaceutical and Medical Equipment Co., Ltd.');
?>
<a href="#main-content" class="sr-only z-[100] rounded bg-white px-4 py-3 text-primary focus:not-sr-only focus:fixed focus:left-3 focus:top-3">
    <?php echo esc_html(tk_home_text('Bỏ qua đến nội dung', 'Skip to content')); ?>
</a>

<header class="relative z-40 bg-white shadow-sm">
    <div class="hidden bg-primary text-white lg:block">
        <div class="tk-container flex min-h-10 items-center justify-between gap-5 text-sm">
            <div class="flex items-center gap-6">
                <?php if ($hotline) : ?>
                    <a class="inline-flex min-h-11 items-center gap-2 hover:text-blue-100" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $hotline)); ?>">
                        <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.79 19.79 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.69 2.8a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0122 16.92z"/></svg>
                        <span><?php echo esc_html(tk_home_text('Hotline', 'Hotline')); ?>: <?php echo esc_html($hotline); ?></span>
                    </a>
                <?php endif; ?>
                <?php if ($email) : ?>
                    <a class="inline-flex min-h-11 items-center gap-2 hover:text-blue-100" href="mailto:<?php echo esc_attr($email); ?>">
                        <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="m22 6-10 7L2 6"/></svg>
                        <span><?php echo esc_html($email); ?></span>
                    </a>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-1">
                <?php if (function_exists('wpm_language_switcher')) : ?>
                    <div data-dropdown data-open="false" class="relative">
                        <button type="button" data-dropdown-trigger aria-expanded="false" class="inline-flex min-h-11 items-center gap-2 px-3 hover:bg-white/10">
                            <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/></svg>
                            <?php echo esc_html(tk_home_text('Ngôn ngữ', 'Language')); ?>
                        </button>
                        <div class="tk-dropdown-panel tk-language-list min-w-36 text-slate-700"><?php wpm_language_switcher('list', 'name'); ?></div>
                    </div>
                <?php endif; ?>
                <div data-dropdown data-open="false" class="relative">
                    <button type="button" data-dropdown-trigger aria-expanded="false" class="inline-flex min-h-11 items-center gap-2 px-3 hover:bg-white/10">
                        <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        <?php echo esc_html(tk_home_text('Tìm kiếm', 'Search')); ?>
                    </button>
                    <div class="tk-dropdown-panel w-80">
                        <form role="search" action="<?php echo esc_url(home_url('/')); ?>" method="get" class="flex gap-2">
                            <label for="site-search" class="sr-only"><?php echo esc_html(tk_home_text('Từ khóa tìm kiếm', 'Search keywords')); ?></label>
                            <input id="site-search" name="s" type="search" class="min-h-11 min-w-0 flex-1 rounded-lg border border-slate-300 px-3 text-slate-800" placeholder="<?php echo esc_attr(tk_home_text('Nhập từ khóa...', 'Enter keywords...')); ?>">
                            <button class="min-h-11 rounded-lg bg-button px-4 font-bold text-white" type="submit"><?php echo esc_html(tk_home_text('Tìm', 'Search')); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sticky top-0 bg-white">
        <div class="tk-container hidden min-h-24 items-center justify-between gap-8 lg:flex">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="flex shrink-0 items-center" aria-label="<?php echo esc_attr($company_name); ?>">
                <img src="<?php echo esc_url(tk_home_logo()); ?>" width="200" height="139" class="h-auto w-[154px]" alt="<?php echo esc_attr($company_name); ?>">
            </a>
            <nav aria-label="<?php echo esc_attr(tk_home_text('Điều hướng chính', 'Primary navigation')); ?>" class="min-w-0">
                <?php tk_home_desktop_menu($home_menu); ?>
            </nav>
        </div>

        <div class="tk-container flex min-h-[72px] items-center justify-between lg:hidden">
            <button type="button" data-menu-open aria-controls="site-mobile-menu" aria-expanded="false" class="flex size-11 items-center justify-center rounded-lg text-primary" aria-label="<?php echo esc_attr(tk_home_text('Mở menu', 'Open menu')); ?>">
                <svg class="size-7" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($company_name); ?>">
                <img src="<?php echo esc_url(tk_home_logo()); ?>" width="200" height="139" class="h-[58px] w-auto" alt="<?php echo esc_attr($company_name); ?>">
            </a>
            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $hotline)); ?>" class="flex size-11 items-center justify-center rounded-lg text-primary" aria-label="<?php echo esc_attr(tk_home_text('Gọi hotline', 'Call hotline')); ?>">
                <svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.79 19.79 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.69 2.8a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0122 16.92z"/></svg>
            </a>
        </div>
    </div>
</header>

<div data-menu-overlay class="tk-overlay fixed inset-0 z-50 bg-slate-950/55 lg:hidden"></div>
<aside id="site-mobile-menu" data-menu-drawer aria-hidden="true" inert class="tk-drawer fixed inset-y-0 left-0 z-[60] w-[min(88vw,360px)] overflow-y-auto bg-white p-5 shadow-2xl lg:hidden">
    <div class="mb-5 flex items-center justify-between border-b border-slate-200 pb-4">
        <img src="<?php echo esc_url(tk_home_logo()); ?>" width="200" height="139" class="h-14 w-auto" alt="<?php echo esc_attr($company_name); ?>">
        <button type="button" data-menu-close class="flex size-11 items-center justify-center rounded-lg text-primary" aria-label="<?php echo esc_attr(tk_home_text('Đóng menu', 'Close menu')); ?>">
            <svg class="size-7" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
    </div>
    <form role="search" action="<?php echo esc_url(home_url('/')); ?>" method="get" class="mb-5 flex gap-2">
        <label class="sr-only" for="mobile-search"><?php echo esc_html(tk_home_text('Từ khóa tìm kiếm', 'Search keywords')); ?></label>
        <input id="mobile-search" name="s" type="search" class="min-h-11 min-w-0 flex-1 rounded-lg border border-slate-300 px-3" placeholder="<?php echo esc_attr(tk_home_text('Tìm kiếm...', 'Search...')); ?>">
        <button class="flex size-11 items-center justify-center rounded-lg bg-button text-white" type="submit" aria-label="<?php echo esc_attr(tk_home_text('Tìm', 'Search')); ?>">
            <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        </button>
    </form>
    <nav aria-label="<?php echo esc_attr(tk_home_text('Menu di động', 'Mobile navigation')); ?>"><?php tk_home_mobile_menu($home_menu); ?></nav>
    <?php if (function_exists('wpm_language_switcher')) : ?>
        <div class="tk-language-list mt-6 border-t border-slate-200 pt-4"><?php wpm_language_switcher('list', 'name'); ?></div>
    <?php endif; ?>
</aside>
