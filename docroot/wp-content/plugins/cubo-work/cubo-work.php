<?php

/**
 * Plugin Name: Cubo Work
 * Description: Work area plugin for the Cubo dashboard — job tracker and other work-related features.
 * Version: 1.0.0
 * Author: João Dias
 * Text Domain: cubo-work
 * Requires at least: 6.0
 * Tested up to: 7.0
 * Requires PHP: 8.3
 */

if (! defined('ABSPATH')) {
    exit;
}

define('CUBO_WORK_VERSION', '1.0.0');
define('CUBO_WORK_PATH', plugin_dir_path(__FILE__));
define('CUBO_WORK_URL', plugin_dir_url(__FILE__));

require_once CUBO_WORK_PATH . 'vendor/autoload.php';

register_activation_hook(__FILE__, [Cubo\Work\Activator::class, 'activate']);

add_action('plugins_loaded', function () {
    if (! defined('CUBO_CORE_VERSION')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>' .
                esc_html__('Cubo Work requires Cubo Core to be installed and active.', 'cubo-work') .
                '</p></div>';
        });
        add_action('admin_init', function () {
            deactivate_plugins(plugin_basename(__FILE__));
        });
        return;
    }

    (new Cubo\Work\Cubo_Work())->run();
});
