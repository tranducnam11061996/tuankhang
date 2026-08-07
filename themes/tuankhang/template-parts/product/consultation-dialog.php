<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<dialog class="tk-product-dialog" data-product-modal aria-labelledby="product-form-title">
    <section data-product-modal-panel>
        <div class="tk-product-dialog-header">
            <div>
                <p><?php echo esc_html(tk_home_text('Tư vấn sản phẩm', 'Product consultation')); ?></p>
                <h2 id="product-form-title"><?php echo esc_html(tk_home_text('Trao đổi với chuyên gia Tuấn Khang', 'Speak with a Tuan Khang expert')); ?></h2>
            </div>
            <button type="button" data-product-modal-close aria-label="<?php echo esc_attr(tk_home_text('Đóng biểu mẫu', 'Close form')); ?>">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <p class="tk-product-dialog-intro"><?php echo esc_html(tk_home_text('Để lại thông tin, đội ngũ của chúng tôi sẽ liên hệ và tư vấn theo nhu cầu thực tế của bạn.', 'Leave your details and our team will contact you with advice tailored to your needs.')); ?></p>
        <div class="tk-product-form" data-product-form-host></div>
        <template data-product-form-template>
            <?php
            $form_id = absint(tk_site_option('integrations.contact_form_id'));
            $form_available = $form_id && shortcode_exists('contact-form-7') && get_post_status($form_id) === 'publish';
            echo $form_available
                ? do_shortcode('[contact-form-7 id="' . $form_id . '"]')
                : '<p>' . esc_html(tk_home_text('Biểu mẫu hiện chưa khả dụng.', 'The form is currently unavailable.')) . '</p>';
            ?>
        </template>
    </section>
</dialog>
