<?php
get_header();
$fields = tk_home_fields();
$company_name = tk_home_text('Công ty TNHH Dược và Thiết bị y tế Tuấn Khang', 'Tuan Khang Pharmaceutical and Medical Equipment Co., Ltd.');
$read_more = tk_home_text('Xem thêm', 'Learn more');

$news = array();
$systems = array();
$products = array();
$projects = array();
$partners = array();
for ($i = 1; $i <= 5; $i++) {
    $news[] = array('image' => tk_home_field('duytv_news_image_' . $i), 'title' => tk_home_field('duytv_news_title_' . $i), 'link' => tk_home_url(tk_home_field('duytv_news_link_' . $i)));
    $systems[] = array('image' => tk_home_field('duytv_system_image_' . $i), 'title' => tk_home_field('duytv_system_title_' . $i), 'link' => tk_home_url(tk_home_field('duytv_system_link_' . $i)));
}
for ($i = 1; $i <= 8; $i++) {
    $products[] = array('image' => tk_home_field('wpcf-anh-san-pham-' . $i), 'title' => tk_home_field('wpcf-ten-hien-thi-san-pham-' . $i), 'link' => tk_home_url(tk_home_field('wpcf-link-lien-ket-san-pham-' . $i)));
}
for ($i = 1; $i <= 6; $i++) {
    $projects[] = array('image' => tk_home_field('wpcf-hinh-anh-du-an-' . $i), 'title' => tk_home_field('wpcf-ten-du-an-' . $i), 'link' => tk_home_url(tk_home_field('wpcf-link-du-an-' . $i, '#')));
}
for ($i = 1; $i <= 10; $i++) {
    $partner = tk_home_field('wpcf-doi-tac-' . $i);
    if ($partner) $partners[] = $partner;
}

function tk_render_home_mosaic($items, $read_more)
{
    $classes = array(
        'lg:col-span-2 lg:row-span-2',
        'lg:col-span-1',
        'lg:col-span-1',
        'lg:col-span-1',
        'lg:col-span-1',
    );
    foreach ($items as $index => $item) {
        if (!$item['image'] && !$item['title']) continue;
        $link = $item['link'] ?: '#';
        echo '<article class="tk-card group ' . esc_attr($classes[$index] ?? '') . '">';
        if ($item['image']) {
            echo '<a href="' . esc_url($link) . '" class="tk-card-image block">' . tk_home_picture($item['image'], 'news', array('alt' => $item['title'], 'class' => 'h-full w-full object-cover')) . '</a>';
        }
        echo '<div class="p-5">';
        if ($item['title']) echo '<h3 class="text-lg font-bold leading-snug text-primary"><a class="transition hover:text-accent" href="' . esc_url($link) . '">' . esc_html($item['title']) . '</a></h3>';
        if ($item['link']) echo '<a class="mt-3 inline-flex min-h-11 items-center font-bold text-button hover:text-accent" href="' . esc_url($link) . '">' . esc_html($read_more) . ' <span aria-hidden="true" class="ml-2">→</span></a>';
        echo '</div></article>';
    }
}
?>

<main id="main-content">
    <h1 class="sr-only"><?php echo esc_html($company_name); ?></h1>

    <section data-hero class="relative aspect-[1702/630] min-h-[230px] w-full overflow-hidden bg-primary" aria-roledescription="carousel" aria-label="<?php echo esc_attr(tk_home_text('Banner nổi bật', 'Featured banners')); ?>">
        <?php for ($i = 1; $i <= 2; $i++) : $hero = tk_home_field('wpcf-anhbanner-' . $i); if (!$hero) continue; ?>
            <div data-hero-slide data-active="<?php echo $i === 1 ? 'true' : 'false'; ?>" class="tk-hero-slide" role="group" aria-label="<?php echo esc_attr(sprintf('%d / 2', $i)); ?>">
                <?php echo tk_home_picture($hero, 'hero', array('alt' => $company_name, 'class' => 'h-full w-full object-cover', 'loading' => $i === 1 ? 'eager' : 'lazy', 'fetchpriority' => $i === 1 ? 'high' : 'low')); ?>
            </div>
        <?php endfor; ?>
        <button type="button" data-hero-prev class="absolute left-2 top-1/2 z-10 flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-slate-950/35 text-white transition hover:bg-slate-950/60 sm:left-5" aria-label="<?php echo esc_attr(tk_home_text('Banner trước', 'Previous banner')); ?>">
            <svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <button type="button" data-hero-next class="absolute right-2 top-1/2 z-10 flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-slate-950/35 text-white transition hover:bg-slate-950/60 sm:right-5" aria-label="<?php echo esc_attr(tk_home_text('Banner sau', 'Next banner')); ?>">
            <svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
        </button>
        <div class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 gap-2">
            <?php for ($i = 0; $i < 2; $i++) : ?><button type="button" data-hero-dot aria-current="<?php echo $i === 0 ? 'true' : 'false'; ?>" class="flex size-11 items-center justify-center rounded-full" aria-label="<?php echo esc_attr(sprintf(tk_home_text('Đến banner %d', 'Go to banner %d'), $i + 1)); ?>"><span class="size-3 rounded-full <?php echo $i === 0 ? 'bg-white' : 'bg-white/50'; ?>" aria-hidden="true"></span></button><?php endfor; ?>
        </div>
    </section>

    <?php if (tk_home_field('duytv_story_title') || tk_home_field('duytv_story_content') || tk_home_field('duytv_story_image')) : ?>
        <section class="tk-section bg-slate-50">
            <div class="tk-container grid items-center gap-8 md:grid-cols-2 md:gap-12">
                <div>
                    <h2 class="text-2xl font-bold uppercase leading-tight text-primary md:text-[30px]"><?php echo esc_html(tk_home_field('duytv_story_title')); ?></h2>
                    <div class="tk-richtext mt-5 text-justify leading-7"><?php echo wp_kses_post(tk_home_field('duytv_story_content')); ?></div>
                    <?php if (tk_home_field('duytv_story_link')) : ?><a class="tk-btn mt-6" href="<?php echo esc_url(tk_home_url(tk_home_field('duytv_story_link'))); ?>"><?php echo esc_html($read_more); ?></a><?php endif; ?>
                </div>
                <div class="order-last overflow-hidden rounded-card shadow-card"><?php echo tk_home_picture(tk_home_field('duytv_story_image'), 'story', array('alt' => tk_home_field('duytv_story_title'), 'class' => 'aspect-[16/10] h-full w-full object-cover')); ?></div>
            </div>
        </section>
    <?php endif; ?>

    <section class="tk-section bg-primary text-white">
        <div class="tk-container grid gap-5 md:grid-cols-3">
            <?php for ($i = 1; $i <= 3; $i++) : $title = tk_home_field('duytv_info_title_' . $i); $description = tk_home_field('duytv_info_des_' . $i); if (!$title && !$description) continue; ?>
                <article class="rounded-card border border-white/20 bg-white/10 p-6 text-center backdrop-blur-sm">
                    <h2 class="text-xl font-bold uppercase"><?php echo esc_html($title); ?></h2>
                    <div class="tk-richtext mt-3 text-blue-50"><?php echo wp_kses_post($description); ?></div>
                </article>
            <?php endfor; ?>
        </div>
    </section>

    <section class="tk-section bg-white">
        <div class="tk-container">
            <h2 class="tk-title"><?php echo esc_html(tk_home_field('duytv_news_name', tk_home_text('Tin tức', 'News'))); ?></h2>
            <div class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-4"><?php tk_render_home_mosaic($news, $read_more); ?></div>
            <?php if (tk_home_field('duytv_news_link_all')) : ?><div class="mt-9 text-center"><a class="tk-btn" href="<?php echo esc_url(tk_home_field('duytv_news_link_all')); ?>"><?php echo esc_html(tk_home_text('Xem toàn bộ tin tức', 'View all news')); ?></a></div><?php endif; ?>
        </div>
    </section>

    <section class="tk-section bg-slate-50">
        <div class="tk-container">
            <h2 class="tk-title"><?php echo esc_html(tk_home_field('duytv_system_name', tk_home_text('Hệ thống Implant', 'Implant systems'))); ?></h2>
            <div class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-4"><?php tk_render_home_mosaic($systems, $read_more); ?></div>
            <?php if (tk_home_field('duytv_system_link_all')) : ?><div class="mt-9 text-center"><a class="tk-btn" href="<?php echo esc_url(tk_home_field('duytv_system_link_all')); ?>"><?php echo esc_html(tk_home_text('Xem toàn bộ', 'View all')); ?></a></div><?php endif; ?>
        </div>
    </section>

    <section class="tk-section bg-white">
        <div class="tk-container">
            <h2 class="tk-title"><?php echo esc_html(tk_home_text('Sản phẩm nổi bật', 'Featured products')); ?></h2>
            <div class="mt-9 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($products as $product) : if (!$product['image'] && !$product['title']) continue; ?>
                    <article class="tk-card group flex flex-col">
                        <?php if ($product['image']) : ?><a class="block aspect-[4/3] overflow-hidden bg-white p-4" href="<?php echo esc_url($product['link'] ?: '#'); ?>"><?php echo tk_home_picture($product['image'], 'product', array('alt' => $product['title'], 'class' => 'h-full w-full object-contain transition duration-500 group-hover:scale-[1.03]')); ?></a><?php endif; ?>
                        <div class="flex flex-1 flex-col p-5"><h3 class="text-center text-lg font-bold text-primary"><a class="hover:text-accent" href="<?php echo esc_url($product['link'] ?: '#'); ?>"><?php echo esc_html($product['title']); ?></a></h3><a class="mt-auto inline-flex min-h-11 items-end justify-center pt-3 font-bold text-button hover:text-accent" href="<?php echo esc_url($product['link'] ?: '#'); ?>"><?php echo esc_html($read_more); ?> <span class="ml-2" aria-hidden="true">→</span></a></div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="mt-9 text-center"><a class="tk-btn" href="https://tuankhangmedical.com/san-pham"><?php echo esc_html(tk_home_text('Xem toàn bộ sản phẩm', 'View all products')); ?></a></div>
        </div>
    </section>

    <section class="tk-section bg-slate-50">
        <div class="tk-container">
            <h2 class="tk-title"><?php echo esc_html(tk_home_text('Dự án tiêu biểu', 'Featured projects')); ?></h2>
            <div class="mt-9 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($projects as $project) : if (!$project['image']) continue; ?>
                    <article class="tk-card group sm:last:col-span-2 lg:last:col-span-1 lg:last:col-start-2"><a href="<?php echo esc_url($project['link'] ?: '#'); ?>" class="tk-card-image block"><?php echo tk_home_picture($project['image'], 'project', array('alt' => $project['title'], 'class' => 'h-full w-full object-cover')); ?></a><h3 class="p-3 text-center font-bold text-slate-800"><a class="inline-flex min-h-11 w-full items-center justify-center px-2 hover:text-accent" href="<?php echo esc_url($project['link'] ?: '#'); ?>"><?php echo esc_html($project['title']); ?></a></h3></article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="tk-section bg-primary text-white">
        <div class="tk-container">
            <h2 class="text-center text-2xl font-bold uppercase md:text-[30px]"><?php echo esc_html(tk_home_text('Hành trình thực hiện ước mơ', 'Our journey')); ?></h2>
            <?php $stats = array(array(100000, '+', tk_home_text('Khách hàng', 'Customers')), array(30, '+', tk_home_text('Hãng sản xuất', 'Manufacturers')), array(35, '+', tk_home_text('Container mỗi năm', 'Containers per year')), array(24, '/7', tk_home_text('Hỗ trợ khách hàng', 'Customer support')), array(12, '+', tk_home_text('Năm kinh nghiệm', 'Years of experience'))); ?>
            <div class="mt-9 grid grid-cols-2 gap-5 md:grid-cols-5">
                <?php foreach ($stats as $index => $stat) : ?><div class="<?php echo $index === 4 ? 'col-span-2 mx-auto w-1/2 md:col-span-1 md:w-auto' : ''; ?> rounded-card border border-white/20 bg-white/10 p-5 text-center"><p class="text-3xl font-bold md:text-4xl"><span data-counter="<?php echo esc_attr($stat[0]); ?>">0</span><?php echo esc_html($stat[1]); ?></p><p class="mt-2 text-sm font-bold uppercase text-blue-100"><?php echo esc_html($stat[2]); ?></p></div><?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ($partners) : ?>
        <section class="tk-section overflow-hidden bg-white">
            <div class="tk-container"><h2 class="tk-title"><?php echo esc_html(tk_home_text('Đối tác của chúng tôi', 'Our partners')); ?></h2></div>
            <div class="mt-9 overflow-hidden" tabindex="0" aria-label="<?php echo esc_attr(tk_home_text('Danh sách đối tác', 'Partner list')); ?>">
                <div class="tk-partner-track flex items-center gap-10 px-5">
                    <?php for ($copy = 0; $copy < 2; $copy++) : ?><div class="flex items-center gap-10" <?php echo $copy ? 'aria-hidden="true"' : ''; ?>><?php foreach ($partners as $partner) : ?><div class="flex h-24 w-40 shrink-0 items-center justify-center rounded-xl border border-slate-100 bg-white p-3"><?php echo tk_home_picture($partner, 'partner', array('alt' => $copy ? '' : tk_home_text('Logo đối tác', 'Partner logo'), 'class' => 'max-h-full w-full object-contain')); ?></div><?php endforeach; ?></div><?php endfor; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
