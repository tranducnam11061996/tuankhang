<?php
get_header();

$company_name = tk_home_text('Công ty TNHH Dược và Thiết bị y tế Tuấn Khang', 'Tuan Khang Pharmaceutical and Medical Equipment Co., Ltd.');
$read_more = tk_home_text('Xem chi tiết', 'View details');
$view_all = tk_home_text('Xem tất cả', 'View all');
$hotline = (string) tk_site_field('wpcf-so-hotline');

$hero = array(
    'eyebrow' => tk_home_copy('tk_home_hero_eyebrow', 'Hơn một thập kỷ đồng hành cùng bác sĩ nha khoa', 'More than a decade supporting dental professionals'),
    'title' => tk_home_copy('tk_home_hero_title', 'Nâng chuẩn điều trị nha khoa Việt Nam', 'Advancing dental care in Vietnam'),
    'description' => tk_home_copy('tk_home_hero_description', 'Tuấn Khang kết nối bác sĩ và phòng khám với thiết bị, vật liệu và hệ thống Implant được tuyển chọn từ những thương hiệu uy tín trên thế giới.', 'Tuan Khang connects dentists and clinics with carefully selected equipment, materials and implant systems from trusted global manufacturers.'),
    'image' => tk_home_hero_image(),
    'secondary_image' => tk_home_hero_secondary_image(),
    'primary_label' => tk_home_copy('tk_home_hero_primary_label', 'Nhận tư vấn chuyên môn', 'Get expert consultation'),
    'primary_url' => tk_home_url(tk_home_field('tk_home_hero_primary_url'), home_url('/lien-he/')),
    'secondary_label' => tk_home_copy('tk_home_hero_secondary_label', 'Khám phá sản phẩm', 'Explore products'),
    'secondary_url' => tk_home_url(tk_home_field('tk_home_hero_secondary_url'), home_url('/san-pham/')),
);

$metric_defaults = array(
    array('100000', '+', tk_home_text('Khách hàng', 'Customers')),
    array('30', '+', tk_home_text('Hãng sản xuất', 'Manufacturers')),
    array('35', '+', tk_home_text('Container mỗi năm', 'Containers per year')),
    array('24', '/7', tk_home_text('Hỗ trợ chuyên môn', 'Expert support')),
    array('12', '+', tk_home_text('Năm kinh nghiệm', 'Years of experience')),
);
$metrics = array();
foreach ($metric_defaults as $index => $default) {
    $number = $index + 1;
    $value = trim((string) tk_home_field('tk_home_metric_value_' . $number, $default[0]));
    $metrics[] = array(
        'value' => $value !== '' ? $value : $default[0],
        'target' => (int) preg_replace('/[^0-9]/', '', $value !== '' ? $value : $default[0]),
        'suffix' => tk_home_copy('tk_home_metric_suffix_' . $number, $default[1], $default[1]),
        'label' => tk_home_copy('tk_home_metric_label_' . $number, $default[2], $default[2]),
    );
}
$hero_proof_indexes = array(4, 1, 3);
$hero_proofs = array();
foreach ($hero_proof_indexes as $index) {
    if (isset($metrics[$index])) {
        $hero_proofs[] = $metrics[$index];
    }
}

$partners = array();
for ($index = 1; $index <= 10; $index++) {
    $image = tk_home_field('wpcf-doi-tac-' . $index);
    if ($image) $partners[] = $image;
}

$values = array();
$value_icons = array('target', 'mission', 'vision');
for ($index = 1; $index <= 3; $index++) {
    $title = tk_home_field('duytv_info_title_' . $index);
    $description = tk_home_field('duytv_info_des_' . $index);
    if (!$title && !$description) continue;
    $values[] = array('title' => $title, 'description' => $description, 'icon' => $value_icons[$index - 1]);
}

$capability_defaults = array(
    array(tk_home_text('Danh mục được tuyển chọn', 'Curated portfolio'), tk_home_text('Thiết bị, vật liệu và hệ thống Implant chính hãng từ các nhà sản xuất đã được kiểm chứng.', 'Authentic equipment, materials and implant systems from proven manufacturers.'), home_url('/san-pham/'), 'portfolio'),
    array(tk_home_text('Hỗ trợ lâm sàng', 'Clinical support'), tk_home_text('Đồng hành cùng bác sĩ từ lựa chọn giải pháp đến hướng dẫn sử dụng và hỗ trợ chuyên môn.', 'Supporting dentists from solution selection through product guidance and clinical assistance.'), home_url('/dich-vu-ho-tro-phau-thuat/'), 'clinical'),
    array(tk_home_text('Phân phối toàn quốc', 'Nationwide distribution'), tk_home_text('Năng lực cung ứng ổn định cùng hệ thống chi nhánh tại Hà Nội và Thành phố Hồ Chí Minh.', 'Reliable supply supported by branches in Hanoi and Ho Chi Minh City.'), home_url('/lien-he/'), 'distribution'),
);
$capabilities = array();
foreach ($capability_defaults as $index => $default) {
    $number = $index + 1;
    $capabilities[] = array(
        'title' => tk_home_copy('tk_home_capability_title_' . $number, $default[0], $default[0]),
        'description' => tk_home_copy('tk_home_capability_description_' . $number, $default[1], $default[1]),
        'link' => tk_home_url(tk_home_field('tk_home_capability_link_' . $number), $default[2]),
        'icon' => $default[3],
    );
}

$systems = array();
$news = array();
for ($index = 1; $index <= 5; $index++) {
    $system = array('image' => tk_home_field('duytv_system_image_' . $index), 'title' => tk_home_field('duytv_system_title_' . $index), 'link' => tk_home_url(tk_home_field('duytv_system_link_' . $index), ''));
    if ($system['image'] && $system['title'] && count($systems) < 4) $systems[] = $system;
    $news_item = array('image' => tk_home_field('duytv_news_image_' . $index), 'title' => tk_home_field('duytv_news_title_' . $index), 'link' => tk_home_url(tk_home_field('duytv_news_link_' . $index), ''));
    if ($news_item['image'] && $news_item['title'] && count($news) < 3) $news[] = $news_item;
}

$products = array();
for ($index = 1; $index <= 8; $index++) {
    $product = array('image' => tk_home_field('wpcf-anh-san-pham-' . $index), 'title' => tk_home_field('wpcf-ten-hien-thi-san-pham-' . $index), 'link' => tk_home_url(tk_home_field('wpcf-link-lien-ket-san-pham-' . $index), ''));
    if ($product['image'] && $product['title'] && count($products) < 4) $products[] = $product;
}

$projects = array();
for ($index = 1; $index <= 6; $index++) {
    $project = array('image' => tk_home_field('wpcf-hinh-anh-du-an-' . $index), 'title' => tk_home_field('wpcf-ten-du-an-' . $index), 'link' => tk_home_url(tk_home_field('wpcf-link-du-an-' . $index), ''));
    if ($project['image'] && $project['title'] && count($projects) < 3) $projects[] = $project;
}

$story_title = tk_home_field('duytv_story_title', tk_home_text('Câu chuyện về Tuấn Khang', 'The Tuan Khang story'));
$story_content = tk_home_field('duytv_story_content');
$story_image = tk_home_field('duytv_story_image');
$story_link = tk_home_url(tk_home_field('duytv_story_link'), home_url('/gioi-thieu/'));
$capability_image = $hero['secondary_image'] ?: ($news[1]['image'] ?? ($story_image ?: $hero['image']));
$final_cta = array(
    'title' => tk_home_copy('tk_home_cta_title', 'Cùng Tuấn Khang lựa chọn giải pháp phù hợp cho phòng khám', 'Choose the right solution for your clinic with Tuan Khang'),
    'description' => tk_home_copy('tk_home_cta_description', 'Đội ngũ chuyên môn sẵn sàng tư vấn về hệ thống Implant, thiết bị và vật liệu nha khoa.', 'Our specialists are ready to advise on implant systems, dental equipment and materials.'),
    'label' => tk_home_copy('tk_home_cta_label', 'Nhận tư vấn chuyên môn', 'Get expert consultation'),
    'url' => tk_home_url(tk_home_field('tk_home_cta_url'), home_url('/lien-he/')),
);
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
                    <div class="md:w-48" data-reveal><p class="tk-eyebrow"><?php echo esc_html(tk_home_text('Mạng lưới quốc tế', 'Global network')); ?></p><h2 id="partner-title" class="mt-2 font-display text-2xl font-semibold text-primary"><?php echo esc_html(tk_home_text('Đối tác đồng hành', 'Trusted partners')); ?></h2></div>
                    <div class="tk-partner-cloud" tabindex="0" aria-label="<?php echo esc_attr(tk_home_text('Danh sách đối tác quốc tế', 'International partner list')); ?>">
                        <?php foreach ($partners as $partner) : ?><div class="tk-partner-logo"><?php echo tk_home_picture($partner, 'partner', array('alt' => tk_home_text('Logo đối tác Tuấn Khang', 'Tuan Khang partner logo'), 'class' => 'max-h-full w-full object-contain')); ?></div><?php endforeach; ?>
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
                    <p class="tk-eyebrow"><?php echo esc_html(tk_home_text('Câu chuyện thương hiệu', 'Our story')); ?></p>
                    <h2 class="tk-display-title mt-4"><?php echo esc_html($story_title); ?></h2>
                    <blockquote class="tk-story-quote"><?php echo esc_html(tk_home_text('Uy tín được xây dựng từ sự thấu hiểu nhu cầu điều trị và năng lực đồng hành lâu dài.', 'Trust is built through clinical understanding and the ability to stand alongside customers for the long term.')); ?></blockquote>
                    <div class="tk-richtext mt-6 max-w-[65ch] text-pretty leading-8"><?php echo wp_kses_post($story_content); ?></div>
                    <div class="mt-7"><?php tk_home_render_cta($read_more, $story_link, 'secondary'); ?></div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($values) : ?>
        <section class="tk-home-section bg-surface">
            <div class="tk-container">
                <?php tk_home_render_heading(tk_home_text('Giá trị dẫn đường', 'What guides us'), tk_home_text('Mục tiêu · Sứ mệnh · Tầm nhìn', 'Purpose · Mission · Vision'), tk_home_text('Ba cam kết định hướng cách Tuấn Khang lựa chọn sản phẩm, phục vụ khách hàng và phát triển dài hạn.', 'Three commitments guide how Tuan Khang selects products, serves customers and grows for the long term.')); ?>
                <div class="mt-10 grid items-stretch gap-5 md:grid-cols-3">
                    <?php foreach ($values as $index => $value) : ?><article class="tk-value-card" data-reveal data-reveal-order="<?php echo esc_attr($index + 1); ?>"><div class="tk-value-top"><div class="tk-line-icon"><?php echo tk_home_icon($value['icon']); ?></div><span>0<?php echo esc_html($index + 1); ?></span></div><h3><?php echo esc_html($value['title']); ?></h3><div class="tk-richtext mt-4 text-pretty leading-7 text-muted"><?php echo wp_kses_post($value['description']); ?></div></article><?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="tk-capability-section bg-tech text-white">
        <div class="tk-container grid items-center gap-12 py-16 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-7">
                <?php tk_home_render_heading(tk_home_copy('tk_home_capability_eyebrow', 'Nền tảng vận hành', 'Operational foundation'), tk_home_copy('tk_home_capability_title', 'Năng lực tạo nên khác biệt', 'Capabilities that make the difference'), tk_home_copy('tk_home_capability_description', 'Tuấn Khang kết hợp danh mục sản phẩm được tuyển chọn, hỗ trợ chuyên môn và năng lực phân phối để đồng hành lâu dài cùng bác sĩ.', 'Tuan Khang combines a curated portfolio, expert support and nationwide distribution to build lasting partnerships with dental professionals.'), 'dark'); ?>
                <div class="mt-9 space-y-3">
                    <?php foreach ($capabilities as $index => $capability) : ?><a class="tk-capability-card" href="<?php echo esc_url($capability['link']); ?>" data-reveal data-reveal-order="<?php echo esc_attr($index + 1); ?>"><span class="tk-capability-index">0<?php echo esc_html($index + 1); ?></span><span class="min-w-0"><strong><?php echo esc_html($capability['title']); ?></strong><small><?php echo esc_html($capability['description']); ?></small></span><svg class="tk-cta-arrow ml-auto size-5 shrink-0" aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h12M11 5l5 5-5 5"/></svg></a><?php endforeach; ?>
                </div>
            </div>
            <figure class="tk-capability-media lg:col-span-5" data-reveal data-reveal-order="2"><?php echo tk_home_picture($capability_image, 'capability', array('alt' => tk_home_text('Hoạt động chuyên môn và hợp tác quốc tế của Tuấn Khang', 'Tuan Khang professional and international partnership activities'), 'class' => 'h-full w-full object-cover')); ?><figcaption><strong>30+</strong><span><?php echo esc_html(tk_home_text('thương hiệu và nhà sản xuất đồng hành', 'partner brands and manufacturers')); ?></span></figcaption></figure>
        </div>
    </section>

    <?php if ($systems) : ?>
        <section class="tk-home-section bg-white">
            <div class="tk-container">
                <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                    <?php tk_home_render_heading(tk_home_text('Giải pháp cấy ghép', 'Implant solutions'), tk_home_field('duytv_system_name', tk_home_text('Hệ thống Implant', 'Implant systems')), tk_home_text('Các hệ thống được lựa chọn theo tiêu chí ổn định, chính xác và phù hợp thực hành lâm sàng.', 'Systems selected for stability, precision and clinical relevance.')); ?>
                    <?php if (tk_home_field('duytv_system_link_all')) : ?><div data-reveal><?php tk_home_render_cta($view_all, tk_home_url(tk_home_field('duytv_system_link_all')), 'secondary'); ?></div><?php endif; ?>
                </div>
                <div class="mt-10 grid items-stretch gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <?php foreach ($systems as $index => $item) : ?><article class="tk-home-card" data-reveal data-reveal-order="<?php echo esc_attr(($index % 4) + 1); ?>"><?php if ($item['link']) : ?><a class="tk-home-card-media" href="<?php echo esc_url($item['link']); ?>"><?php else : ?><div class="tk-home-card-media"><?php endif; ?><?php echo tk_home_picture($item['image'], 'product', array('alt' => $item['title'], 'class' => 'h-full w-full object-cover')); ?><?php echo $item['link'] ? '</a>' : '</div>'; ?><div class="tk-home-card-body"><h3 class="tk-card-title"><?php echo esc_html($item['title']); ?></h3><?php if ($item['link']) : ?><a class="tk-text-link mt-auto" href="<?php echo esc_url($item['link']); ?>"><?php echo esc_html($read_more); ?><span aria-hidden="true">→</span></a><?php endif; ?></div></article><?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($products) : ?>
        <section class="tk-home-section bg-surface">
            <div class="tk-container">
                <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between"><?php tk_home_render_heading(tk_home_text('Danh mục tuyển chọn', 'Curated portfolio'), tk_home_text('Sản phẩm nổi bật', 'Featured products'), tk_home_text('Những sản phẩm được quan tâm trong hệ sinh thái thiết bị và vật liệu nha khoa Tuấn Khang.', 'Selected products from the Tuan Khang dental equipment and materials portfolio.')); ?><div data-reveal><?php tk_home_render_cta($view_all, home_url('/san-pham/'), 'secondary'); ?></div></div>
                <div class="mt-10 grid items-stretch gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <?php foreach ($products as $index => $item) : ?><article class="tk-home-card tk-product-card" data-reveal data-reveal-order="<?php echo esc_attr(($index % 4) + 1); ?>"><?php if ($item['link']) : ?><a class="tk-home-card-media" href="<?php echo esc_url($item['link']); ?>"><?php else : ?><div class="tk-home-card-media"><?php endif; ?><?php echo tk_home_picture($item['image'], 'product', array('alt' => $item['title'], 'class' => 'h-full w-full object-contain')); ?><?php echo $item['link'] ? '</a>' : '</div>'; ?><div class="tk-home-card-body"><h3 class="tk-card-title"><?php echo esc_html($item['title']); ?></h3><?php if ($item['link']) : ?><a class="tk-text-link mt-auto" href="<?php echo esc_url($item['link']); ?>"><?php echo esc_html($read_more); ?><span aria-hidden="true">→</span></a><?php endif; ?></div></article><?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($projects) : ?>
        <section class="tk-home-section bg-white">
            <div class="tk-container">
                <?php tk_home_render_heading(tk_home_text('Năng lực triển khai', 'Delivery capability'), tk_home_text('Dự án tiêu biểu', 'Featured projects'), tk_home_text('Kinh nghiệm cung cấp giải pháp cho bệnh viện, phòng khám và các đơn vị y tế.', 'Experience delivering solutions for hospitals, clinics and healthcare organisations.')); ?>
                <div class="tk-project-grid mt-10">
                    <?php foreach ($projects as $index => $item) : ?><article class="tk-project-card <?php echo $index === 0 ? 'tk-project-featured' : ''; ?>" data-reveal data-reveal-order="<?php echo esc_attr($index + 1); ?>"><?php if ($item['link']) : ?><a class="tk-project-media" href="<?php echo esc_url($item['link']); ?>"><?php else : ?><div class="tk-project-media"><?php endif; ?><?php echo tk_home_project_picture($index, $item['image'], $item['title']); ?><?php echo $item['link'] ? '</a>' : '</div>'; ?><div class="tk-project-body"><p><?php echo esc_html($index === 0 ? tk_home_text('Case study nổi bật', 'Featured case study') : tk_home_text('Dự án đã triển khai', 'Delivered project')); ?></p><h3><?php echo esc_html($item['title']); ?></h3><?php if ($item['link']) : ?><a class="tk-text-link mt-auto" href="<?php echo esc_url($item['link']); ?>"><?php echo esc_html($read_more); ?><span aria-hidden="true">→</span></a><?php endif; ?></div></article><?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($news) : ?>
        <section class="tk-home-section bg-surface">
            <div class="tk-container">
                <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between"><?php tk_home_render_heading(tk_home_text('Kiến thức và hoạt động', 'Knowledge and activities'), tk_home_field('duytv_news_name', tk_home_text('Tin tức chuyên môn', 'Professional insights')), tk_home_text('Cập nhật công nghệ, hội thảo và hoạt động hợp tác dành cho cộng đồng nha khoa.', 'Technology updates, seminars and partnerships for the dental community.')); ?><?php if (tk_home_field('duytv_news_link_all')) : ?><div data-reveal><?php tk_home_render_cta($view_all, tk_home_url(tk_home_field('duytv_news_link_all')), 'secondary'); ?></div><?php endif; ?></div>
                <div class="tk-news-grid mt-10">
                    <?php foreach ($news as $index => $item) : ?><article class="tk-news-card" data-reveal data-reveal-order="<?php echo esc_attr($index + 1); ?>"><?php if ($item['link']) : ?><a class="tk-news-media" href="<?php echo esc_url($item['link']); ?>"><?php else : ?><div class="tk-news-media"><?php endif; ?><?php echo tk_home_picture($item['image'], 'news', array('alt' => $item['title'], 'class' => 'h-full w-full object-cover')); ?><?php echo $item['link'] ? '</a>' : '</div>'; ?><div class="tk-news-body"><p><?php echo esc_html(tk_home_text('Góc chuyên môn', 'Insights')); ?></p><h3><?php echo esc_html($item['title']); ?></h3><?php if ($item['link']) : ?><a class="tk-text-link mt-auto" href="<?php echo esc_url($item['link']); ?>"><?php echo esc_html($read_more); ?><span aria-hidden="true">→</span></a><?php endif; ?></div></article><?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

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
