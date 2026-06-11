<?php

namespace Cubo\Core\Admin;

use Cubo\Core\Loader;

/**
 * Manages the WP admin dashboard: removes default widgets, adds the Cubo widget.
 */
class Dashboard
{
    /**
     * Registers all hooks with the loader.
     *
     * @param Loader $loader The plugin hook loader.
     */
    public function register(Loader $loader): void
    {
        $loader->add_action('wp_dashboard_setup', $this, 'remove_default_widgets');
        $loader->add_action('wp_dashboard_setup', $this, 'add_cubo_widget');
    }

    /**
     * Removes all default WP dashboard widgets.
     */
    public function remove_default_widgets(): void
    {
        remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
        remove_meta_box('dashboard_activity', 'dashboard', 'normal');
        remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
        remove_meta_box('dashboard_primary', 'dashboard', 'side');
        remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
        remove_meta_box('health_check_dashboard_widget', 'dashboard', 'normal');
    }

    /**
     * Adds the Cubo dashboard widget.
     */
    public function add_cubo_widget(): void
    {
        wp_add_dashboard_widget(
            'cubo_dashboard_widget',
            __('Cubo', 'cubo-core'),
            [ $this, 'render_cubo_widget' ]
        );
    }

    /**
     * Renders the Cubo dashboard widget content.
     */
    public function render_cubo_widget(): void
    {
        echo '<p>' . esc_html__('Welcome to Cubo. Select a feature from the menu.', 'cubo-core') . '</p>';
    }
}
