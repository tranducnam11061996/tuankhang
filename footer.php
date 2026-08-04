<?php
$hotline = (string) tk_site_field('wpcf-so-hotline');
$email = (string) tk_site_field('wpcf-email');
$company_name = tk_site_text('Công ty TNHH Dược và Thiết bị y tế Tuấn Khang', 'Tuan Khang Pharmaceutical and Medical Equipment Co., Ltd.');
$footer_menu = wp_get_nav_menu_items(29);
$transparent_pixel = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=';
$socials = array(
    array('field' => 'wpcf-link-fanpage', 'label' => 'Facebook', 'icon' => 'icon-1.png'),
    array('field' => 'wpcf-link-twitter', 'label' => 'Twitter', 'icon' => 'icon-2.png'),
    array('field' => 'wpcf-link-google-plus', 'label' => 'Google', 'icon' => 'icon-3.png'),
    array('field' => 'wpcf-link-instagram', 'label' => 'Instagram', 'icon' => 'icon-4.png'),
    array('field' => 'wpcf-link-kenh-youtube', 'label' => 'YouTube', 'icon' => 'icon-5.png'),
);
?>
<footer id="footer" class="tk-footer bg-tech text-blue-100">
    <div class="tk-container py-14 md:py-16">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-12 lg:gap-8">
            <section class="lg:col-span-4">
                <h2 class="sr-only"><?php echo esc_html(tk_site_text('Thông tin công ty', 'Company information')); ?></h2>
                <a class="tk-footer-brand" href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php echo esc_attr($transparent_pixel); ?>" data-deferred-src="<?php echo esc_url(tk_site_logo(false)); ?>" width="200" height="139" class="tk-footer-brand-logo" decoding="async" alt="<?php echo esc_attr($company_name); ?>"><noscript><img src="<?php echo esc_url(tk_site_logo(false)); ?>" width="200" height="139" class="tk-footer-brand-logo" alt="<?php echo esc_attr($company_name); ?>"></noscript></a>
                <p class="mt-6 max-w-sm text-pretty leading-7 text-blue-100"><?php echo esc_html(tk_site_text('Hơn một thập kỷ đồng hành cùng bác sĩ và phòng khám với thiết bị, vật liệu và giải pháp nha khoa được tuyển chọn.', 'More than a decade supporting dentists and clinics with carefully selected equipment, materials, and dental solutions.')); ?></p>
                <div class="mt-6 space-y-3 text-sm">
                    <?php if ($hotline) : ?><a class="tk-footer-contact" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $hotline)); ?>"><span class="tk-footer-contact-icon">☎</span><span><small>Hotline</small><strong><?php echo esc_html($hotline); ?></strong></span></a><?php endif; ?>
                    <?php if ($email) : ?><a class="tk-footer-contact" href="mailto:<?php echo esc_attr($email); ?>"><span class="tk-footer-contact-icon">@</span><span><small>Email</small><strong><?php echo esc_html($email); ?></strong></span></a><?php endif; ?>
                </div>
            </section>

            <nav class="lg:col-span-2" aria-label="<?php echo esc_attr(tk_site_text('Dịch vụ khách hàng', 'Customer services')); ?>">
                <h2 class="tk-footer-heading"><?php echo esc_html(tk_site_text('Điều hướng', 'Navigation')); ?></h2>
                <ul class="mt-5 space-y-1"><?php if ($footer_menu && !is_wp_error($footer_menu)) : foreach ($footer_menu as $item) : ?><li><a class="tk-footer-link" href="<?php echo esc_url($item->url); ?>"><span><?php echo esc_html($item->title); ?></span><span aria-hidden="true">→</span></a></li><?php endforeach; endif; ?></ul>
            </nav>

            <section class="lg:col-span-3">
                <h2 class="tk-footer-heading"><?php echo esc_html(tk_site_text('Hệ thống chi nhánh', 'Branch network')); ?></h2>
                <address class="mt-5 space-y-5 not-italic text-sm leading-6 text-blue-100">
                    <p><strong class="block text-white"><?php echo esc_html(tk_site_text('Hà Nội', 'Hanoi')); ?></strong><?php echo esc_html(tk_site_text('Số 23, ngõ 38 Phương Mai, Phường Kim Liên, Hà Nội.', 'No. 23, Alley 38 Phuong Mai, Kim Lien Ward, Hanoi.')); ?></p>
                    <p><strong class="block text-white"><?php echo esc_html(tk_site_text('Hồ Chí Minh', 'Ho Chi Minh City')); ?></strong><?php echo esc_html(tk_site_text('Số 1/1 Đường Hoàng Việt, Phường Tân Sơn Nhất, Hồ Chí Minh.', 'No. 1/1 Hoang Viet Street, Tan Son Nhat Ward, Ho Chi Minh City.')); ?></p>
                    <p><strong class="block text-white"><?php echo esc_html(tk_site_text('Trụ sở', 'Head office')); ?></strong><?php echo esc_html(tk_site_field('wpcf-dia-chi-cong-ty')); ?></p>
                </address>
            </section>

            <section data-map-section class="lg:col-span-3">
                <h2 class="tk-footer-heading"><?php echo esc_html(tk_site_text('Vị trí Tuấn Khang', 'Find Tuan Khang')); ?></h2>
                <div class="mt-5 aspect-[4/3] overflow-hidden rounded-2xl border border-white/10 bg-footer-deep"><iframe title="<?php echo esc_attr(tk_site_text('Bản đồ trụ sở Tuấn Khang', 'Tuan Khang office map')); ?>" data-map-src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.685746165307!2d105.83404037627878!3d21.005230488584314!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ac7919c6b95b%3A0x624a289a6ba5cdc!2zMjMgTmcuIDM4IFAuIFBoxrDGoW5nIE1haSwgS2ltIExpw6puLCDEkOG7kW5nIMSQYSwgSMOgIE7hu5lpLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2s!4v1734943915820!5m2!1svi!2s" class="h-full w-full border-0" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe></div>
            </section>
        </div>

        <div class="mt-12 flex flex-col gap-7 border-t border-white/10 pt-8 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-white"><?php echo esc_html(tk_site_text('Kết nối với Tuấn Khang', 'Connect with Tuan Khang')); ?></h2>
                <div class="mt-4 flex flex-wrap gap-3"><?php foreach ($socials as $social) : $url = tk_site_field($social['field']); if (!$url) continue; $icon_url = get_theme_file_uri('image/' . $social['icon']); ?><a href="<?php echo esc_url($url); ?>" class="tk-social-link" aria-label="<?php echo esc_attr($social['label']); ?>" rel="noopener"><img src="<?php echo esc_attr($transparent_pixel); ?>" data-deferred-src="<?php echo esc_url($icon_url); ?>" width="38" height="38" decoding="async" alt=""><noscript><img src="<?php echo esc_url($icon_url); ?>" width="38" height="38" alt=""></noscript></a><?php endforeach; ?></div>
            </div>
            <a class="block w-40 rounded-lg bg-white p-2 transition-transform duration-200 hover:-translate-y-0.5 focus-visible:-translate-y-0.5" rel="nofollow noopener" target="_blank" href="http://online.gov.vn/Home/WebDetails/84000" title="<?php echo esc_attr(tk_site_text('Đã thông báo với Bộ Công Thương', 'Registered with Ministry of Industry and Trade')); ?>"><img src="https://tuankhangmedical.com/wp-content/uploads/2021/08/bct.png" width="160" height="60" loading="lazy" decoding="async" alt="<?php echo esc_attr(tk_site_text('Đã thông báo với Bộ Công Thương', 'Registered with Ministry of Industry and Trade')); ?>"></a>
        </div>
    </div>
    <div class="border-t border-white/10 bg-footer-deep py-4 text-center text-xs text-blue-200"><div class="tk-container">© <?php echo esc_html(date('Y')); ?> TUẤN KHANG. All Rights Reserved.</div></div>
</footer>

<?php if ($hotline && !is_singular('san-pham')) : ?><a class="tk-floating-call fixed bottom-5 left-5 z-40 flex size-14 items-center justify-center rounded-full bg-action text-white shadow-xl lg:hidden" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $hotline)); ?>" aria-label="<?php echo esc_attr(tk_site_text('Gọi hotline', 'Call hotline')); ?>"><svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.79 19.79 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.69 2.8a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0122 16.92z"/></svg></a><?php endif; ?>
<div id="fb-customer-chat" class="fb-customerchat" page_id="108890274790969" attribution="biz_inbox"></div>
<?php wp_footer(); ?>
</body>
</html>
