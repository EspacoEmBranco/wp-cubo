<?php

namespace Cubo\Core\Admin;

use Cubo\Core\Loader;

/**
 * Registers the top-level Cubo admin menu and removes irrelevant default items.
 */
class Menu
{
    /**
     * Registers all hooks with the loader.
     *
     * @param Loader $loader The plugin hook loader.
     */
    public function register(Loader $loader): void
    {
        $loader->add_action('admin_menu', $this, 'register_cubo_menu');
        $loader->add_action('admin_menu', $this, 'remove_default_menu_items', 999);
    }

    /**
     * Registers the top-level Cubo menu page and fires an action for feature plugins to attach submenus.
     */
    public function register_cubo_menu(): void
    {
        add_menu_page(
            __('Cubo', 'cubo-core'),
            __('Cubo', 'cubo-core'),
            'manage_options',
            'cubo',
            [ $this, 'render_menu_page' ],
            'dashicons-grid-view',
            2
        );

        /**
         * Fires after the Cubo top-level menu is registered.
         * Feature plugins hook here to register their submenus.
         */
        do_action('cubo_register_menus');
    }

    /**
     * Renders the Cubo main menu page.
     */
    public function render_menu_page(): void
    {
        echo '<div class="wrap"><h1>' . esc_html__('Cubo Dashboard', 'cubo-core') . '</h1></div>';
    }

    /**
     * Removes default admin menu items not relevant to Cubo.
     */
    public function remove_default_menu_items(): void
    {
        remove_menu_page('edit.php');          // Posts
        remove_menu_page('edit-comments.php'); // Comments
    }
}
