<?php
if (!defined('ABSPATH')) {
    exit;
}

$args = isset($args) && is_array($args) ? $args : array();
$label = (string) ($args['label'] ?? tk_home_text('Trao đổi với chuyên gia', 'Speak with an expert'));
$source = (string) ($args['source'] ?? 'final');
$hotline = (string) ($args['hotline'] ?? tk_site_option('contact.hotline'));
$zalo_url = tk_theme_zalo_url();
$messenger_url = (string) tk_site_option('social.messenger_url');
?>
<div class="tk-product-consultation-primary">
    <button
        type="button"
        class="tk-product-consultation-form-trigger"
        data-product-modal-open
        data-cta-source="<?php echo esc_attr($source); ?>"
        data-tk-event="tk_product_cta_click"
        data-tk-action="consultation"
        data-tk-placement="<?php echo esc_attr($source); ?>"
    >
        <span class="tk-product-consultation-form-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6.75 4.25h10.5A1.75 1.75 0 0 1 19 6v13.25H8.5A2.5 2.5 0 0 1 6 16.75V5a.75.75 0 0 1 .75-.75Z"/><path d="M9.25 4.25v15M12.25 8h3.75M12.25 11h3.75"/></svg>
        </span>
        <span><?php echo esc_html($label); ?></span>
    </button>
    <span class="tk-product-consultation-primary-divider" aria-hidden="true"></span>
    <div class="tk-product-consultation-socials" role="group" aria-label="<?php echo esc_attr(tk_home_text('Kênh tư vấn trực tuyến', 'Online consultation channels')); ?>">
        <?php if ($zalo_url) : ?>
            <a class="tk-product-consultation-social tk-product-consultation-social-zalo" href="<?php echo esc_url($zalo_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(tk_home_text('Chat với Tuấn Khang qua Zalo, mở trong tab mới', 'Chat with Tuan Khang on Zalo, opens in a new tab')); ?>" data-tooltip="<?php echo esc_attr(tk_home_text('Chat qua Zalo', 'Chat on Zalo')); ?>" data-tk-event="tk_product_cta_click" data-tk-action="zalo_chat" data-tk-placement="<?php echo esc_attr($source); ?>">
                <svg viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="M11 8.5h26a7 7 0 0 1 7 7v14a7 7 0 0 1-7 7H24l-7.5 4v-4H11a7 7 0 0 1-7-7v-14a7 7 0 0 1 7-7Z" fill="none" stroke="currentColor" stroke-width="2.5"/><text x="24" y="27.5" fill="currentColor" font-family="Arial, sans-serif" font-size="12" font-weight="700" text-anchor="middle">Zalo</text></svg>
            </a>
        <?php endif; ?>
        <?php if ($messenger_url) : ?><a class="tk-product-consultation-social tk-product-consultation-social-messenger" href="<?php echo esc_url($messenger_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(tk_home_text('Chat với Tuấn Khang qua Facebook Messenger, mở trong tab mới', 'Chat with Tuan Khang on Facebook Messenger, opens in a new tab')); ?>" data-tooltip="<?php echo esc_attr(tk_home_text('Chat Facebook', 'Chat on Facebook')); ?>" data-tk-event="tk_product_cta_click" data-tk-action="facebook_chat" data-tk-placement="<?php echo esc_attr($source); ?>">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12 2C6.48 2 2 6.15 2 11.27c0 2.91 1.45 5.5 3.72 7.2V22l3.4-1.87c.91.25 1.88.39 2.88.39 5.52 0 10-4.15 10-9.25S17.52 2 12 2Zm.99 12.48-2.55-2.72-4.98 2.72 5.48-5.82 2.62 2.72 4.91-2.72-5.48 5.82Z"/></svg>
        </a><?php endif; ?>
    </div>
</div>
