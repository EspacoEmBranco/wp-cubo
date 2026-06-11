<?php

namespace Cubo\Core\Users;

use Cubo\Core\Loader;

/**
 * Manages user registration, role setup, and admin access restrictions.
 */
class Users
{
    /**
     * Registers all hooks with the loader.
     *
     * @param Loader $loader The plugin hook loader.
     */
    public function register(Loader $loader): void
    {
        $loader->add_filter('pre_option_users_can_register', $this, 'disable_user_registration');
        $loader->add_action('admin_init', $this, 'restrict_admin_access');
    }

    /**
     * Prevents public user registration without writing to the database.
     *
     * @return int
     */
    public function disable_user_registration(): int
    {
        return 0;
    }

    /**
     * Redirects non-administrator users away from the WP admin.
     */
    public function restrict_admin_access(): void
    {
        if (wp_doing_ajax()) {
            return;
        }

        if (! current_user_can('manage_options')) {
            wp_safe_redirect(home_url());
            exit;
        }
    }
}
