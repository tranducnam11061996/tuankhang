<?php
/*
 * Plugin Name: Reset Customizer
 * Version: 1.1.6
 * Plugin URI: https://webd.uk/support/
 * Description: Adds a reset button to each section in the customizer and easily backup / restore / migrate customizer settings
 * Author: Webd Ltd
 * Author URI: https://webd.uk
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: reset-customizer
 */



if (!defined('ABSPATH')) {
    exit('This isn\'t the page you\'re looking for. Move along, move along.');
}



if (!class_exists('reset_customizer_class')) {

	class reset_customizer_class {

        public static $version = '1.1.6';

		function __construct() {

            if (is_admin()) {

    	        add_action('admin_menu', array($this, 'rc_settings_menu'));
                add_action('wp_ajax_rc_download_theme_mods', array($this,'rc_download_theme_mods'));
                add_action('wp_ajax_rc_delete_theme_mods', array($this,'rc_delete_theme_mods'));
                add_action('wp_ajax_rc_restore_theme_mods', array($this,'rc_restore_theme_mods'));
                add_action('wp_ajax_rc_download_theme_mods', array($this,'rc_download_theme_mods'));
                add_action('wp_ajax_rc_delete_theme_mod', array($this,'rc_delete_theme_mod'));
                add_action('wp_ajax_rc_get_control_defaults', array($this,'rc_get_control_defaults'));
                add_action('customize_controls_enqueue_scripts', array($this, 'rc_enqueue_customize_controls_js'));
                add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'rc_add_plugin_action_link'));
                add_action('admin_notices', 'rcCommon::admin_notices');
                add_action('wp_ajax_dismiss_rc_notice_handler', 'rcCommon::ajax_notice_handler');

            }

		}

		function rc_add_plugin_action_link($links) {

			$settings_links = rcCommon::plugin_action_links(add_query_arg('page', 'reset_customizer', admin_url('themes.php')));

			return array_merge($settings_links, $links);

		}

        function rc_enqueue_customize_controls_js() {

            wp_enqueue_script('rc-customize-controls', plugins_url('js/customize-controls.js', __FILE__), array('jquery', 'customize-controls'), rcCommon::plugin_version(), true);
            $l10n = array();
            $l10n['resetPrefix'] = __('Reset', 'reset-customizer');
            $l10n['resetSuffix'] = __('Defaults', 'reset-customizer');
            $l10n['nonce'] = wp_create_nonce('get-control-defaults');
            $l10n['errorNotice'] = __('Something went wrong!', 'reset-customizer');
            wp_localize_script(
                'rc-customize-controls',
                'resetCustomizer',
                $l10n
            );

        }

		function rc_settings_menu() {

		    add_theme_page(__('Reset Customizer', 'reset-customizer'), __('Backup Customizer', 'reset-customizer'), 'manage_options', 'reset_customizer', array($this, 'rc_settings_page'), 2);
            add_action('admin_init', array($this, 'rc_register_settings'));

		}

        function rc_settings_page() {

?>
<div class="wrap">
<h1><?php esc_html_e('Reset Customizer', 'reset-customizer'); ?></h1>
<p><?php esc_html_e('All sets of Customizer Theme Modifications found in your database are listed below. You can download (backup), delete or even restore a set to the active theme. Deleting a set of theme modifications will reset all modifications for that theme. Restoring a set of theme modifications will completely overwrite the existing modifications for the active theme.', 'reset-customizer'); ?></p>
<?php

            $all_options = wp_load_alloptions();
            $theme_mods  = array();
 
            foreach ($all_options as $key => $value) {

                if (substr($key, 0, 11) === 'theme_mods_') {

                    $theme_mods[substr($key, 11)] = $value;

                }

            }

            if ($theme_mods) {

?>
<table class="wp-list-table widefat striped">
<thead>
<tr>
<th class="manage-column column-name column-primary"><?php esc_html_e('Theme', 'reset-customizer'); ?></th>
<th class="manage-column column-actions"><?php esc_html_e('Actions', 'reset-customizer'); ?></th>
</tr>
</thead>
<tbody>
<?php

                $i = 0;

                foreach ($theme_mods as $key => $value) {

                    $i++;

?>
<tr class="theme-mods-<?php echo esc_attr(htmlentities($key)); ?>">
<td class="plugin-title column-primary">
<strong><?php echo esc_html(htmlentities($key)); ?></strong>
<?php

                    if ($key == get_stylesheet()) {

                        echo 'Active Theme';

                    } elseif ($key == get_template()) {

                        echo 'Parent Theme';

                    }

?>
</td>
<td class="column-actions">
<span class="rc-download button button-small" data-theme="<?php echo esc_html($key); ?>"><?php esc_html_e('Download', 'reset-customizer'); ?></span>
<span class="rc-delete button button-small" data-theme="<?php echo esc_html($key); ?>"><?php esc_html_e('Delete', 'reset-customizer'); ?></span>
</td>
</tr>

<?php

                }

?>
</tbody>
</table>
<?php

            }

?>
<input type="file" id="rc-json-file" accept=".json" style="display: none;" />
<p><span class="rc-restore button button-small" data-theme="<?php echo esc_html($key); ?>"><?php esc_html_e('Restore', 'reset-customizer'); ?></span></p>
<script type="text/javascript">
(function($) {
    $('.rc-download').click(function() {
        var data = {
        	action: 'rc_download_theme_mods',
        	security: '<?php echo esc_attr(wp_create_nonce('download-theme-mods')); ?>',
        	theme: $(this).data('theme')
        };
	    $.ajax({
    	    url: ajaxurl,
    	    dataType: 'text',
    	    data: data,
            type: 'POST',
            success: function(themeModsJson) {
                var themeModsFile = new Blob(
                    [themeModsJson], {
                        type : "application/json;charset=utf-8"
                    }
                );
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth() + 1;
                var yyyy = today.getFullYear();
                if (dd < 10) {
                    dd = '0' + dd;
                }
                if (mm < 10) {
                    mm = '0' + mm;
                }
                today = yyyy + '_' + mm + '_' + dd;
                var downloadLink = document.createElement('a');
                var downloadURL = window.URL.createObjectURL(themeModsFile);
                downloadLink.href = downloadURL;
                downloadLink.download = data.theme + '_' + today + '.json';
                document.body.append(downloadLink);
                downloadLink.click();
                downloadLink.remove();
                window.URL.revokeObjectURL(downloadURL);
            },
            error: function() {
                alert('<?php esc_html_e('Something went wrong!', 'reset-customizer'); ?>');
            }
	    });
    });
    $('.rc-delete').click(function() {
        var confirmText = '<?php
/* translators: current theme slug */
esc_html_e('Are you sure you want to delete all the %s theme modifications?', 'reset-customizer'); ?>';
        if (confirm(confirmText.replace('%s', $(this).data('theme')))) {
            var data = {
            	action: 'rc_delete_theme_mods',
            	security: '<?php echo esc_attr(wp_create_nonce('delete-theme-mods')); ?>',
            	theme: $(this).data('theme')
            };
    	    $.ajax({
        	    url: ajaxurl,
        	    data: data,
                type: 'POST',
                success: function() {
                    $('.theme-mods-' + data.theme).fadeTo('slow', 0, function() {
                        $('.theme-mods-' + data.theme).remove();
                    });
                },
                error: function() {
                    alert('<?php esc_attr_e('Something went wrong!', 'reset-customizer'); ?>');
                }
    	    });
        }
    });
    $('.rc-restore').click(function() {
        document.getElementById('rc-json-file').click();
    });
    $('#rc-json-file').change(function() {
        var confirmText = '<?php
/* translators: current theme slug */
esc_attr_e('Are you sure you want to upload %s to the active theme?', 'reset-customizer'); ?>';
        if (confirm(confirmText.replace('%s', $('#rc-json-file').prop('files')[0].name))) {
            var data = new FormData();
            data.append('action', 'rc_restore_theme_mods');
            data.append('security', '<?php echo esc_attr(wp_create_nonce('restore-theme-mods')); ?>');
            data.append('file', $('#rc-json-file').prop('files')[0]);
    	    $.ajax({
        	    url: ajaxurl,
        	    data: data,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function() {
                    $('#rc-json-file').val('');
                    alert('Theme modifications have been successfully restored to the active theme.');
                },
                error: function() {
                    $('#rc-json-file').val('');
                    alert('<?php esc_attr_e('Something went wrong!', 'reset-customizer'); ?>');
                }
    	    });
        }
	});
})(jQuery);
</script>
<!-- <form action="options.php" method="post">
<?php

            settings_fields('reset_customizer');

?>
<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes','reset-customizer'); ?>"></p>
</form> -->
<?php

            $active_theme_mods = get_theme_mods();

            if ($active_theme_mods) {

                ksort($active_theme_mods);

            if (!class_exists('WP_Customize_Manager')) {

                require_once(ABSPATH . 'wp-includes/class-wp-customize-manager.php');

            }

?>
<h2><?php esc_html_e('Active Theme Mods', 'reset-customizer'); ?></h2>

<p><span id="toggle-active-theme-mods" class="button button-small" data-mod="<?php echo esc_html($key); ?>"><?php esc_html_e('Show', 'reset-customizer'); ?></span></p>

<table id="active-theme-mods" class="wp-list-table widefat striped" style="display: none;">
<thead>
<tr>
<td class="manage-column column-cb check-column"></td>
<th class="manage-column column-name column-primary">Mod</th>
<th class="manage-column column-description">Value</th>
</tr>
</thead>
<tbody>
<?php

                $i = 0;

                foreach ($active_theme_mods as $key => $value) {

                    $i++;
?>

<tr class="theme-mod-<?php echo esc_attr(htmlentities($key)); ?>">
<td class="check-column"><?php echo esc_html($i); ?>)</td>
<td class="plugin-title column-primary"><?php echo esc_html($key); ?> <span class="rc-delete-mod button button-small" data-mod="<?php echo esc_html($key); ?>"><?php esc_html_e('Delete', 'reset-customizer'); ?></span></td>
<td class="column-description"><pre></pre><?php
// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
echo esc_html(print_r($value, true)); ?></pre></td>
</tr>

<?php

                }

?>
</tbody>
</table>
<script type="text/javascript">
(function($) {
    $('#toggle-active-theme-mods').click(function() {
        $('#active-theme-mods').toggle('slow', function() {
            $('#toggle-active-theme-mods').text(function(i, text){
                return text === 'Show' ? 'Hide' : 'Show';
            });
        });
    });
    $('.rc-delete-mod').click(function() {
        var confirmText = '<?php
/* translators: theme mod key */
esc_attr_e('Are you sure you want to delete theme mod "%s"?', 'reset-customizer'); ?>';
        if (confirm(confirmText.replace('%s', $(this).data('mod')))) {
            var data = {
            	action: 'rc_delete_theme_mod',
            	security: '<?php echo esc_attr(wp_create_nonce('delete-theme-mod')); ?>',
            	mod: $(this).data('mod')
            };
    	    $.ajax({
        	    url: ajaxurl,
        	    data: data,
                type: 'POST',
                success: function() {
                    $('.theme-mod-' + data.mod).fadeTo('slow', 0, function() {
                        $('.theme-mod-' + data.mod).remove();
                    });
                },
                error: function() {
                    alert('<?php esc_attr_e('Something went wrong!', 'reset-customizer'); ?>');
                }
    	    });
        }
    });
})(jQuery);
</script>
<?php

            }

?>
</div>
<?php

        }

        function rc_register_settings() {

        	register_setting(
        	    'reset_customizer',
        	    'reset_customizer',
        	    array($this, 'rc_options_validate')
            );

        }

        function rc_options_validate($input) {

            $options = get_option('reset_customizer');

            return $options;

        }

        function rc_download_theme_mods() {

            check_ajax_referer('download-theme-mods', 'security');

            if (current_user_can('manage_options') && isset($_POST['theme']) && sanitize_key($_POST['theme'])) {

                $theme_mods = get_option('theme_mods_' . sanitize_key($_POST['theme']));

                if ($theme_mods) {

                    wp_send_json($theme_mods);

                } else {

                    wp_send_json_error();

                }
    
            } else {

                wp_send_json_error();

            }

        	wp_die();

        }

        function rc_delete_theme_mods() {

            check_ajax_referer('delete-theme-mods', 'security');

            if (current_user_can('manage_options') && isset($_POST['theme']) && sanitize_key($_POST['theme'])) {

                if (delete_option('theme_mods_' . sanitize_key($_POST['theme']))) {

                    wp_send_json_success();

                } else {

                    wp_send_json_error();

                }

            } else {

                wp_send_json_error();

            }

        	wp_die();

        }

        function rc_restore_theme_mods() {

            check_ajax_referer('restore-theme-mods', 'security');

            if (isset($_FILES['file']['tmp_name']) && isset($_FILES['file']['type']) && $_FILES['file']['type'] == 'application/json' && current_user_can('manage_options')) {

                $json_data = file_get_contents(sanitize_text_field($_FILES['file']['tmp_name']));
                $theme_mods = false;

                if ($json_data) {

                    $theme_mods = json_decode($json_data, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {

                        $theme_mods = false;

                    }

                }

                if ($theme_mods) {

                    $old_theme_mods = get_option('theme_mods_' . get_stylesheet());

                    if ($old_theme_mods) {

                        update_option('theme_mods_' . get_stylesheet() . '_bak', $old_theme_mods);

                    }

                    update_option('theme_mods_' . get_stylesheet(), $theme_mods);

                    wp_send_json_success();

                } else {

                    wp_send_json_error();

                }

            } else {

                wp_send_json_error();

            }

        	wp_die();

        }

        function rc_delete_theme_mod() {

            check_ajax_referer('delete-theme-mod', 'security');

            if (current_user_can('manage_options') && isset($_POST['mod']) && sanitize_key($_POST['mod'])) {

                remove_theme_mod(sanitize_key($_POST['mod']));

                wp_send_json_success();

            } else {

                wp_send_json_error();

            }

        	wp_die();

        }

        public function rc_get_control_defaults() {

            check_ajax_referer('get-control-defaults');

            $controls = array();

            if (isset($_POST['controls'])) {

// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                $controls = wp_unslash($_POST['controls']);

            }

            if (
                current_user_can('manage_options') &&
                is_array($controls) &&
                $controls
            ) {

                global $wp_customize;
                $control_defaults = array();

                foreach ($controls as $control) {

                    $control = sanitize_key($control);
                    $setting = $wp_customize->get_setting($control);

                    if ($setting && isset($setting->default)) {

                        $control_defaults[$control] = $setting->default;

                    }

                }

                wp_send_json_success($control_defaults);

            } else {

                wp_send_json_error();

            }

        }

    }

    if (!class_exists('rcCommon')) {

        require_once(dirname(__FILE__) . '/includes/class-rc-common.php');

    }

    $reset_customizer_object = new reset_customizer_class();

}

?>
