<?php
/**
 * Auditable CLI helper for updating/installing the site's approved plugins.
 */

require_once __DIR__ . '/cli-bootstrap.php';

require_once dirname(__DIR__, 4) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

function tk_plugin_cli_option($name)
{
    foreach (array_slice($_SERVER['argv'], 1) as $argument) {
        if (str_starts_with($argument, '--' . $name . '=')) {
            return substr($argument, strlen($name) + 3);
        }
    }
    return '';
}

function tk_plugin_cli_fail($message)
{
    fwrite(STDERR, $message . "\n");
    exit(1);
}

$update = tk_plugin_cli_option('update');
$install = tk_plugin_cli_option('install');
$activate = tk_plugin_cli_option('activate');
$deactivate = tk_plugin_cli_option('deactivate');
$delete = tk_plugin_cli_option('delete');

if ($deactivate !== '') {
    $plugins = array_values(array_filter(array_map('trim', explode(',', $deactivate))));
    deactivate_plugins($plugins, false, false);
    foreach ($plugins as $plugin) {
        if (is_plugin_active($plugin)) {
            tk_plugin_cli_fail('Could not deactivate ' . $plugin);
        }
        echo 'Deactivated ' . $plugin . PHP_EOL;
    }
}

if ($delete !== '') {
    $plugins = array_values(array_filter(array_map('trim', explode(',', $delete))));
    foreach ($plugins as $plugin) {
        if (is_plugin_active($plugin)) {
            tk_plugin_cli_fail('Refusing to delete active plugin ' . $plugin);
        }
    }
    $result = delete_plugins($plugins);
    if (is_wp_error($result)) {
        tk_plugin_cli_fail($result->get_error_message());
    }
    if ($result !== true) {
        tk_plugin_cli_fail('Could not delete all requested plugins.');
    }
    foreach ($plugins as $plugin) {
        echo 'Deleted ' . $plugin . PHP_EOL;
    }
}

if ($update !== '') {
    if (!array_key_exists($update, get_plugins())) {
        tk_plugin_cli_fail('Plugin is not installed: ' . $update);
    }
    $was_active = is_plugin_active($update);
    wp_clean_plugins_cache(true);
    wp_update_plugins();
    $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());
    $result = $upgrader->upgrade($update);
    if (is_wp_error($result)) {
        tk_plugin_cli_fail($result->get_error_message());
    }
    if ($result !== true) {
        tk_plugin_cli_fail('No update was installed for ' . $update);
    }
    wp_clean_plugins_cache(true);
    if ($was_active && !is_plugin_active($update)) {
        $activation = activate_plugin($update, '', false, false);
        if (is_wp_error($activation)) {
            tk_plugin_cli_fail('Updated but could not reactivate ' . $update . ': ' . $activation->get_error_message());
        }
    }
    echo 'Updated ' . $update . ' to ' . get_plugins()[$update]['Version'] . PHP_EOL;
}

if ($install !== '') {
    $api = plugins_api('plugin_information', array('slug' => sanitize_key($install), 'fields' => array('sections' => false)));
    if (is_wp_error($api) || empty($api->download_link)) {
        tk_plugin_cli_fail(is_wp_error($api) ? $api->get_error_message() : 'Plugin download URL is unavailable.');
    }
    $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());
    $result = $upgrader->install($api->download_link);
    if (is_wp_error($result)) {
        tk_plugin_cli_fail($result->get_error_message());
    }
    if ($result !== true) {
        tk_plugin_cli_fail('Could not install ' . $install);
    }
    echo 'Installed ' . $install . PHP_EOL;
}

if ($activate !== '') {
    $result = activate_plugin($activate, '', false, false);
    if (is_wp_error($result)) {
        tk_plugin_cli_fail($result->get_error_message());
    }
    echo 'Activated ' . $activate . PHP_EOL;
}

if ($update === '' && $install === '' && $activate === '' && $deactivate === '' && $delete === '') {
    tk_plugin_cli_fail('Use --update, --install, --activate, --deactivate or --delete.');
}
