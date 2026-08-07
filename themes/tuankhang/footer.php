<?php
$hotline = (string) tk_site_option('contact.hotline');
$email = (string) tk_site_option('contact.email');
$consultation_url = (string) tk_site_option('contact.consultation_url', home_url('/lien-he/'));
$company_name = (string) tk_site_option('brand.company_name', 'Công ty TNHH Dược và Thiết bị y tế Tuấn Khang');
$footer_menu = wp_get_nav_menu_items(tk_theme_menu_id('footer'));
$branches = (array) tk_site_option('branches', array());
$map_embed_url = (string) tk_site_option('map_embed_url');
$certificate_url = (string) tk_site_option('integrations.certificate_url');
$certificate_image_url = tk_theme_attachment_url(absint(tk_site_option('integrations.certificate_image_id')), 'https://tuankhangmedical.com/wp-content/uploads/2021/08/bct.png');
$messenger_url = (string) tk_site_option('social.messenger_url');
$transparent_pixel = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=';
$socials = array(
    array('url' => tk_site_option('social.facebook_url'), 'label' => 'Facebook', 'icon' => 'icon-1.png'),
    array('url' => tk_site_option('social.x_url'), 'label' => 'X', 'icon' => 'icon-2.png'),
    array('url' => tk_site_option('social.instagram_url'), 'label' => 'Instagram', 'icon' => 'icon-4.png'),
    array('url' => tk_site_option('social.youtube_url'), 'label' => 'YouTube', 'icon' => 'icon-5.png'),
    array('url' => tk_theme_zalo_url(), 'label' => 'Zalo', 'short' => 'Z'),
    array('url' => tk_site_option('social.linkedin_url'), 'label' => 'LinkedIn', 'short' => 'in'),
    array('url' => tk_site_option('social.tiktok_url'), 'label' => 'TikTok', 'short' => 'TT'),
);
?>
<footer id="footer" class="tk-footer text-blue-100">
    <div class="tk-container">
        <section class="tk-footer-gateway" aria-labelledby="tk-footer-gateway-title">
            <a class="tk-footer-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(tk_site_text('Về trang chủ Tuấn Khang', 'Go to Tuan Khang homepage')); ?>">
                <img src="<?php echo esc_attr($transparent_pixel); ?>" data-deferred-src="<?php echo esc_url(tk_site_logo(true)); ?>" width="250" height="174" class="tk-footer-brand-logo" decoding="async" alt="<?php echo esc_attr($company_name); ?>">
                <noscript><img src="<?php echo esc_url(tk_site_logo(true)); ?>" width="250" height="174" class="tk-footer-brand-logo" alt="<?php echo esc_attr($company_name); ?>"></noscript>
            </a>

            <div class="tk-footer-gateway-copy">
                <p class="tk-footer-kicker"><?php echo esc_html(tk_site_text('TUẤN KHANG / CLINICAL PARTNER', 'TUAN KHANG / CLINICAL PARTNER')); ?></p>
                <h2 id="tk-footer-gateway-title"><?php echo esc_html(tk_site_text('Công nghệ quốc tế. Đồng hành chuyên môn tại Việt Nam.', 'International technology. Expert partnership in Vietnam.')); ?></h2>
                <p><?php echo esc_html(tk_site_text('Thiết bị, vật liệu và hệ thống Implant được tuyển chọn cho thực tiễn điều trị.', 'Dental equipment, materials and implant systems selected for clinical practice.')); ?></p>
            </div>

            <div class="tk-footer-gateway-actions">
                <a class="tk-footer-primary-cta" href="<?php echo esc_url($consultation_url); ?>" target="_blank">
                    <span><?php echo esc_html(tk_site_text('Liên hệ với Tuấn Khang', 'Talk with Tuan Khang')); ?></span>
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
                <?php if ($hotline) : ?>
                    <a class="tk-footer-hotline" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $hotline)); ?>">
                        <span class="tk-footer-action-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.79 19.79 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.69 2.8a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0122 16.92z"/></svg></span>
                        <span><small><?php echo esc_html(tk_site_text('Hotline hỗ trợ', 'Support hotline')); ?></small><strong><?php echo esc_html($hotline); ?></strong></span>
                    </a>
                <?php endif; ?>
            </div>
        </section>

        <div class="tk-footer-grid">
            <section class="tk-footer-panel">
                <h2 class="tk-footer-heading"><?php echo esc_html(tk_site_text('Thông tin liên hệ', 'Direct contact')); ?></h2>
                <p class="tk-footer-summary"><?php echo esc_html(tk_site_text('Hơn một thập kỷ đồng hành cùng bác sĩ và phòng khám với thiết bị, vật liệu và giải pháp nha khoa được tuyển chọn.', 'More than a decade supporting dentists and clinics with carefully selected equipment, materials, and dental solutions.')); ?></p>
                <div class="tk-footer-contact-list">
                    <?php if ($hotline) : ?>
                        <a class="tk-footer-contact" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $hotline)); ?>">
                            <span class="tk-footer-contact-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.79 19.79 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.69 2.8a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0122 16.92z"/></svg></span>
                            <span><small>Hotline</small><strong><?php echo esc_html($hotline); ?></strong></span>
                        </a>
                    <?php endif; ?>
                    <?php if ($email) : ?>
                        <a class="tk-footer-contact" href="mailto:<?php echo esc_attr($email); ?>">
                            <span class="tk-footer-contact-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg></span>
                            <span><small>Email</small><strong><?php echo esc_html($email); ?></strong></span>
                        </a>
                    <?php endif; ?>
                </div>
            </section>

            <nav class="tk-footer-panel" aria-label="<?php echo esc_attr(tk_site_text('Chính sách khách hàng', 'Customer policies')); ?>">
                <h2 class="tk-footer-heading"><?php echo esc_html(tk_site_text('Chính sách', 'Policies')); ?></h2>
                <ul class="tk-footer-links">
                    <?php if ($footer_menu && !is_wp_error($footer_menu)) : foreach ($footer_menu as $item) : ?>
                        <li><a class="tk-footer-link" href="<?php echo esc_url($item->url); ?>"><span><?php echo esc_html($item->title); ?></span><span aria-hidden="true">→</span></a></li>
                    <?php endforeach; endif; ?>
                </ul>
            </nav>

            <section class="tk-footer-panel">
                <h2 class="tk-footer-heading"><?php echo esc_html(tk_site_text('Hệ thống chi nhánh', 'Branch network')); ?></h2>
                <address class="tk-footer-branches">
                    <?php foreach ($branches as $branch) : if (!is_array($branch) || empty($branch['address'])) continue; ?>
                        <p><strong><?php echo esc_html((string) ($branch['label'] ?? '')); ?></strong><?php echo esc_html((string) $branch['address']); ?></p>
                    <?php endforeach; ?>
                </address>
            </section>

            <section data-map-section class="tk-footer-panel tk-footer-map-panel">
                <h2 class="tk-footer-heading"><?php echo esc_html(tk_site_text('BẢN ĐỒ', 'MAP')); ?></h2>
                <?php if ($map_embed_url) : ?><div class="tk-footer-map-frame"><iframe title="<?php echo esc_attr(tk_site_text('Bản đồ trụ sở Tuấn Khang', 'Tuan Khang office map')); ?>" data-map-src="<?php echo esc_url($map_embed_url); ?>" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe></div><?php endif; ?>
            </section>
        </div>

        <div class="tk-footer-trust">
            <div class="tk-footer-social">
                <h2><?php echo esc_html(tk_site_text('Kết nối với Tuấn Khang', 'Connect with Tuan Khang')); ?></h2>
                <div class="tk-footer-social-list">
                    <?php foreach ($socials as $social) : $url = (string) ($social['url'] ?? ''); if (!$url) continue; ?>
                        <a href="<?php echo esc_url($url); ?>" class="tk-social-link" aria-label="<?php echo esc_attr($social['label']); ?>" target="_blank" rel="noopener noreferrer"><?php if (!empty($social['icon'])) : $icon_url = get_theme_file_uri('image/' . $social['icon']); ?><img src="<?php echo esc_attr($transparent_pixel); ?>" data-deferred-src="<?php echo esc_url($icon_url); ?>" width="38" height="38" decoding="async" alt=""><noscript><img src="<?php echo esc_url($icon_url); ?>" width="38" height="38" alt=""></noscript><?php else : ?><span class="tk-social-short" aria-hidden="true"><?php echo esc_html($social['short']); ?></span><?php endif; ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if ($certificate_url && $certificate_image_url) : ?><a class="tk-footer-cert" rel="nofollow noopener" target="_blank" href="<?php echo esc_url($certificate_url); ?>" title="<?php echo esc_attr(tk_site_text('Đã thông báo với Bộ Công Thương', 'Registered with Ministry of Industry and Trade')); ?>"><img src="<?php echo esc_url($certificate_image_url); ?>" width="160" height="60" loading="lazy" decoding="async" alt="<?php echo esc_attr(tk_site_text('Đã thông báo với Bộ Công Thương', 'Registered with Ministry of Industry and Trade')); ?>"></a><?php endif; ?>
            <p class="tk-footer-copyright">© <?php echo esc_html(date('Y')); ?> TUẤN KHANG.<br><?php echo esc_html(tk_site_text('Bảo lưu mọi quyền.', 'All rights reserved.')); ?></p>
        </div>
    </div>
</footer>

<?php if ($hotline && !is_singular('san-pham')) : ?><a class="tk-floating-call fixed bottom-5 left-5 z-40 flex size-14 items-center justify-center rounded-full bg-action text-white shadow-xl lg:hidden" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $hotline)); ?>" aria-label="<?php echo esc_attr(tk_site_text('Gọi hotline', 'Call hotline')); ?>"><svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.79 19.79 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.69 2.8a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0122 16.92z"/></svg></a><?php endif; ?>
<?php if ($messenger_url) : ?><a class="tk-floating-messenger" href="<?php echo esc_url($messenger_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(tk_site_text('Nhắn tin cho Tuấn Khang qua Messenger', 'Message Tuan Khang on Messenger')); ?>"><svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.15 2 11.27c0 2.91 1.45 5.5 3.72 7.2V22l3.4-1.87c.91.25 1.88.39 2.88.39 5.52 0 10-4.15 10-9.25S17.52 2 12 2zm.99 12.48l-2.55-2.72-4.98 2.72 5.48-5.82 2.62 2.72 4.91-2.72-5.48 5.82z"/></svg></a><?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
