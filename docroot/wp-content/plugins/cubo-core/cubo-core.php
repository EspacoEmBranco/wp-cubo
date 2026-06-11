<?php

/**
 * Plugin Name: Cubo Core
 * Description: Shared foundation for all Cubo plugins - base classes, WP customizations, and admin UI.
 * Version: 1.0.0
 * Author: João Dias
 * Text Domain: cubo-core
 * Requires at least: 6.0
 * Tested up to: 7.0
 * Requires PHP: 8.3
 */

if (! defined('ABSPATH')) {
    exit;
}

define('CUBO_CORE_VERSION', '1.0.0');
define('CUBO_CORE_PATH', plugin_dir_path(__FILE__));
define('CUBO_CORE_URL', plugin_dir_url(__FILE__));

require_once CUBO_CORE_PATH . 'vendor/autoload.php';

register_activation_hook(__FILE__, [Cubo\Core\Activator::class, 'activate']);
register_deactivation_hook(__FILE__, [Cubo\Core\Deactivator::class, 'deactivate']);

add_action('plugins_loaded', function () {
    (new Cubo\Core\Cubo_Core())->run();
});
