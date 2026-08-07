<?php
get_header();

$company_name = (string) tk_site_option('brand.company_name', 'Công ty TNHH Dược và Thiết bị y tế Tuấn Khang');
$read_more = tk_home_text('Xem chi tiết', 'View details');
$view_all = tk_home_text('Xem tất cả', 'View all');
$hotline = (string) tk_site_option('contact.hotline');

$hero = array(
    'eyebrow' => tk_home_option('hero.eyebrow'),
    'title' => tk_home_option('hero.title'),
    'description' => tk_home_option('hero.description'),
    'image' => tk_home_hero_image(),
    'secondary_image' => tk_home_hero_secondary_image(),
    'primary_label' => tk_home_option('hero.primary_label'),
    'primary_url' => tk_home_option('hero.primary_url', home_url('/lien-he/')),
    'secondary_label' => tk_home_option('hero.secondary_label'),
    'secondary_url' => tk_home_option('hero.secondary_url', home_url('/san-pham/')),
);

$metrics = array();
foreach ((array) tk_home_option('metrics', array()) as $metric) {
    $value = trim((string) ($metric['value'] ?? ''));
    $metrics[] = array(
        'value' => $value,
        'target' => (int) preg_replace('/[^0-9]/', '', $value),
        'suffix' => (string) ($metric['suffix'] ?? ''),
        'label' => (string) ($metric['label'] ?? ''),
    );
}
$hero_proof_indexes = array(4, 1, 3);
$hero_proofs = array();
foreach ($hero_proof_indexes as $index) {
    if (isset($metrics[$index])) {
        $hero_proofs[] = $metrics[$index];
    }
}

$partners = (array) tk_home_option('partners', array());

$values = (array) tk_home_option('values', array());

$capability = (array) tk_home_option('capability', array());
$capabilities = array();
foreach ((array) ($capability['items'] ?? array()) as $item) {
    $capabilities[] = array(
        'title' => (string) ($item['title'] ?? ''),
        'description' => (string) ($item['description'] ?? ''),
        'link' => (string) ($item['url'] ?? ''),
        'icon' => (string) ($item['icon'] ?? 'portfolio'),
    );
}

$systems = tk_home_implant_systems();
$products = tk_home_featured_products();
$news = tk_home_latest_news();

$projects = array_values(array_filter(
    array_slice((array) tk_home_option('projects', array()), 0, 5),
    static function ($project) {
        return absint($project['image_id'] ?? 0) > 0 && trim((string) ($project['title'] ?? '')) !== '';
    }
));

$story = (array) tk_home_option('story', array());
$story_title = (string) ($story['title'] ?? '');
$story_content = (string) ($story['content'] ?? '');
$story_image = absint($story['image_id'] ?? 0);
$story_link = (string) ($story['url'] ?? home_url('/gioi-thieu/'));
$capability_image = absint($capability['image_id'] ?? 0) ?: $hero['secondary_image'];
$final_cta = (array) tk_home_option('final_cta', array());
?>

<main id="main-content" class="overflow-hidden">
    <section class="tk-home-hero" aria-labelledby="home-hero-title">
        <div class="tk-container grid items-center gap-10 py-10 md:py-14 lg:min-h-[620px] lg:grid-cols-12 lg:gap-10 lg:py-14">
            <div class="tk-hero-copy lg:col-span-5">
                <p class="tk-eyebrow"><?php echo esc_html($hero['eyebrow']); ?></p>
                <h1 id="home-hero-title" class="tk-hero-title mt-5"><?php echo esc_html($hero['title']); ?></h1>
                <p class="mt-6 max-w-xl text-pretty text-base leading-8 text-muted md:text-lg"><?php echo esc_html($hero['description']); ?></p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <?php tk_home_render_cta($hero['primary_label'], $hero['primary_url']); ?>
                    <?php tk_home_render_cta($hero['secondary_label'], $hero['secondary_url'], 'secondary'); ?>
                </div>
                <?php if ($hero_proofs) : ?>
                    <ul class="tk-hero-proof-list" aria-label="<?php echo esc_attr(tk_home_text('Dấu ấn năng lực Tuấn Khang', 'Tuan Khang capability highlights')); ?>">
                        <?php foreach ($hero_proofs as $proof) : ?>
                            <li><strong><?php echo esc_html($proof['value'] . $proof['suffix']); ?></strong><span><?php echo esc_html($proof['label']); ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="tk-hero-stage lg:col-span-7">
                <figure class="tk-hero-media">
                    <?php echo tk_home_picture($hero['image'], 'hero', array('alt' => tk_home_text('Năng lực thiết bị nha khoa của Tuấn Khang', 'Tuan Khang dental technology capabilities'), 'class' => 'h-full w-full object-cover', 'loading' => 'eager', 'fetchpriority' => 'high')); ?>
                    <figcaption class="tk-hero-caption"><span><?php echo esc_html(tk_home_text('Công nghệ quốc tế', 'International technology')); ?></span><strong><?php echo esc_html(tk_home_text('Tuyển chọn cho thực tiễn điều trị', 'Selected for clinical practice')); ?></strong></figcaption>
                </figure>
                <figure class="tk-hero-proof-media">
                    <?php echo tk_home_picture($hero['secondary_image'], 'hero-proof', array('alt' => tk_home_text('Đội ngũ Tuấn Khang và đối tác tại triển lãm nha khoa quốc tế', 'Tuan Khang team and partners at an international dental exhibition'), 'class' => 'h-full w-full object-cover', 'loading' => 'eager')); ?>
                    <figcaption><span aria-hidden="true">TK</span><?php echo esc_html(tk_home_text('Đội ngũ thật · Năng lực thật', 'Real team · Proven capability')); ?></figcaption>
                </figure>
                <p class="tk-hero-index" aria-hidden="true"><span>01</span><i></i><span>12+</span></p>
            </div>
        </div>
    </section>

    <section class="tk-proof-runway" aria-labelledby="partner-title">
        <div class="tk-proof-metrics">
            <div class="tk-container">
                <div class="tk-metric-grid" data-reveal>
                <?php foreach ($metrics as $index => $metric) : ?>
                    <article class="tk-metric <?php echo $index === 4 ? 'col-span-2 sm:col-span-1' : ''; ?>">
                        <p class="tk-metric-value tabular-nums"><strong data-counter="<?php echo esc_attr($metric['target']); ?>">0</strong><span><?php echo esc_html($metric['suffix']); ?></span></p>
                        <p class="tk-metric-label"><?php echo esc_html($metric['label']); ?></p>
                    </article>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php if ($partners) : ?>
            <div class="tk-proof-partners">
            <div class="tk-container">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:gap-10">
                    <div class="md:w-48" data-reveal><p class="tk-eyebrow"><?php echo esc_html((string) tk_home_option('partners_heading.eyebrow')); ?></p><h2 id="partner-title" class="mt-2 font-display text-2xl font-semibold text-primary"><?php echo esc_html((string) tk_home_option('partners_heading.title')); ?></h2></div>
                    <div class="tk-partner-cloud" tabindex="0" aria-label="<?php echo esc_attr(tk_home_text('Danh sách đối tác quốc tế', 'International partner list')); ?>">
                        <?php foreach ($partners as $partner) : if (!is_array($partner) || empty($partner['image_id'])) continue; ?><div class="tk-partner-logo"><?php echo tk_home_picture($partner['image_id'], 'partner', array('alt' => (string) ($partner['name'] ?? 'Logo đối tác Tuấn Khang'), 'class' => 'max-h-full w-full object-contain')); ?></div><?php endforeach; ?>
                    </div>
                </div>
            </div>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($story_title || $story_content || $story_image) : ?>
        <section class="tk-home-section bg-white">
            <div class="tk-container grid items-center gap-10 lg:grid-cols-12 lg:gap-16">
                <figure class="tk-story-media lg:col-span-6" data-reveal><?php echo tk_home_picture($story_image, 'story', array('alt' => $story_title, 'class' => 'h-full w-full object-cover')); ?><figcaption><?php echo esc_html(tk_home_text('Thiết bị và giải pháp nha khoa được lựa chọn từ thực tiễn.', 'Dental equipment and solutions selected for real clinical needs.')); ?></figcaption></figure>
                <div class="lg:col-span-6" data-reveal data-reveal-order="2">
                    <p class="tk-eyebrow"><?php echo esc_html((string) ($story['eyebrow'] ?? '')); ?></p>
                    <h2 class="tk-display-title mt-4"><?php echo esc_html($story_title); ?></h2>
                    <blockquote class="tk-story-quote"><?php echo esc_html(tk_home_text('Uy tín được xây dựng từ sự thấu hiểu nhu cầu điều trị và năng lực đồng hành lâu dài.', 'Trust is built through clinical understanding and the ability to stand alongside customers for the long term.')); ?></blockquote>
                    <div class="tk-richtext mt-6 max-w-[65ch] text-pretty leading-8"><?php echo wp_kses_post($story_content); ?></div>
                    <div class="mt-7"><?php tk_home_render_cta($read_more, $story_link, 'secondary'); ?></div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($values) : ?>
        <section class="tk-home-section tk-values-section bg-surface">
            <div class="tk-container">
                <?php tk_home_render_heading(tk_home_option('values_heading.eyebrow'), tk_home_option('values_heading.title'), tk_home_option('values_heading.description')); ?>
                <div class="mt-10 grid items-stretch gap-5 md:grid-cols-3">
                    <?php foreach ($values as $index => $value) : ?><article class="tk-value-card" data-reveal data-reveal-order="<?php echo esc_attr($index + 1); ?>"><div class="tk-value-top"><div class="tk-line-icon"><?php echo tk_home_icon($value['icon']); ?></div><h3><?php echo esc_html($value['title']); ?></h3></div><div class="tk-richtext mt-4 text-pretty leading-7 text-muted"><?php echo wp_kses_post($value['description']); ?></div></article><?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="tk-capability-section bg-tech text-white">
        <div class="tk-container grid items-center gap-12 py-16 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-7">
                <?php tk_home_render_heading($capability['eyebrow'] ?? '', $capability['title'] ?? '', $capability['description'] ?? '', 'dark'); ?>
                <div class="mt-9 space-y-3">
                    <?php foreach ($capabilities as $index => $capability_item) : ?><a class="tk-capability-card" href="<?php echo esc_url($capability_item['link']); ?>" data-reveal data-reveal-order="<?php echo esc_attr($index + 1); ?>"><span class="tk-capability-index">0<?php echo esc_html($index + 1); ?></span><span class="min-w-0"><strong><?php echo esc_html($capability_item['title']); ?></strong><small><?php echo esc_html($capability_item['description']); ?></small></span><svg class="tk-cta-arrow ml-auto size-5 shrink-0" aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h12M11 5l5 5-5 5"/></svg></a><?php endforeach; ?>
                </div>
            </div>
            <figure class="tk-capability-media lg:col-span-5" data-reveal data-reveal-order="2"><?php echo tk_home_picture($capability_image, 'capability', array('alt' => tk_home_text('Hoạt động chuyên môn và hợp tác quốc tế của Tuấn Khang', 'Tuan Khang professional and international partnership activities'), 'class' => 'h-full w-full object-cover')); ?><figcaption><strong>30+</strong><span><?php echo esc_html(tk_home_text('thương hiệu và nhà sản xuất đồng hành', 'partner brands and manufacturers')); ?></span></figcaption></figure>
        </div>
    </section>

    <?php get_template_part('template-parts/home/implant-systems', null, array('items' => $systems)); ?>
    <?php get_template_part('template-parts/home/featured-products', null, array('items' => $products)); ?>

    <?php if ($projects) : ?>
        <section class="tk-home-section bg-white">
            <div class="tk-container">
                <?php tk_home_render_heading(tk_home_option('projects_heading.eyebrow'), tk_home_option('projects_heading.title'), tk_home_option('projects_heading.description')); ?>
                <div class="tk-project-grid mt-10">
                    <?php foreach ($projects as $index => $item) : ?>
                        <article class="tk-project-card <?php echo $index === 0 ? 'tk-project-featured' : ''; ?>" data-reveal data-reveal-order="<?php echo esc_attr(min($index + 1, 4)); ?>">
                            <?php echo tk_home_project_picture($item['image_id'] ?? 0, $item['title'] ?? '', $index === 0 ? '(min-width: 1024px) 40vw, (min-width: 768px) 50vw, 100vw' : '(min-width: 1024px) 30vw, (min-width: 768px) 50vw, 50vw'); ?>
                            <div class="tk-project-overlay">
                                <p><?php echo esc_html($item['eyebrow']); ?></p>
                                <h3><?php echo esc_html($item['title']); ?></h3>
                                <span><?php echo esc_html($item['description']); ?></span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php get_template_part('template-parts/home/latest-news', null, array('items' => $news)); ?>

    <section class="bg-white py-12">
        <div class="tk-container">
            <div class="tk-final-cta" data-reveal><span class="tk-final-cta-grid" aria-hidden="true"></span>
                <div><p class="tk-eyebrow text-sky-300"><?php echo esc_html(tk_home_text('Đồng hành cùng bác sĩ', 'Partnering with dental professionals')); ?></p><h2><?php echo esc_html($final_cta['title']); ?></h2><p><?php echo esc_html($final_cta['description']); ?></p></div>
                <div class="flex shrink-0 flex-col gap-3 sm:flex-row lg:flex-col xl:flex-row"><?php tk_home_render_cta($final_cta['label'], $final_cta['url'], 'primary', 'bg-white text-primary hover:bg-sky-50'); ?><?php if ($hotline) : ?><a class="tk-cta tk-cta-secondary border-white/30 text-primary hover:border-white hover:bg-white/10" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $hotline)); ?>"><span><?php echo esc_html($hotline); ?></span><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.79 19.79 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0122 16.92z"/></svg></a><?php endif; ?></div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
