<?php $form_id = wp_unique_id('tk-search-'); ?>
<form role="search" action="<?php echo esc_url(home_url('/')); ?>" method="get" class="flex w-full gap-2">
    <label for="<?php echo esc_attr($form_id); ?>" class="sr-only"><?php echo esc_html(tk_site_text('Từ khóa tìm kiếm', 'Search keywords')); ?></label>
    <input id="<?php echo esc_attr($form_id); ?>" name="s" value="<?php echo esc_attr(get_search_query()); ?>" type="search" class="min-h-11 min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-3 text-slate-800" placeholder="<?php echo esc_attr(tk_site_text('Nhập từ khóa...', 'Enter keywords...')); ?>">
    <button class="inline-flex min-h-11 items-center justify-center rounded-lg bg-button px-4 font-bold text-white" type="submit"><?php echo esc_html(tk_site_text('Tìm', 'Search')); ?></button>
</form>
