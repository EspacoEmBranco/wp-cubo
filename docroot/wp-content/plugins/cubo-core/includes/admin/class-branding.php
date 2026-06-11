<?php

namespace Cubo\Core\Admin;

use Cubo\Core\Loader;

/**
 * Handles admin and login page branding, admin bar customization, and update nag visibility.
 */
class Branding
{
    /**
     * Registers all hooks with the loader.
     *
     * @param Loader $loader The plugin hook loader.
     */
    public function register(Loader $loader): void
    {
        // Login page
        $loader->add_action('login_enqueue_scripts', $this, 'login_styles');
        $loader->add_filter('login_headerurl', $this, 'login_logo_url');
        $loader->add_filter('login_headertext', $this, 'login_logo_title');
        $loader->add_filter('login_redirect', $this, 'after_login_redirect', 10, 3);
        $loader->add_filter('logout_redirect', $this, 'after_logout_redirect', 10, 3);
        $loader->add_filter('authenticate', $this, 'disable_email_login', 20, 3);

        // Admin bar
        $loader->add_action('after_setup_theme', $this, 'hide_admin_bar_for_non_admins');
        $loader->add_action('admin_bar_menu', $this, 'remove_howdy', 25);

        // Admin branding
        $loader->add_filter('admin_footer_text', $this, 'custom_footer_text');
        $loader->add_action('admin_head', $this, 'remove_color_scheme_picker');

        // Update nag
        $loader->add_action('admin_menu', $this, 'hide_update_nag_for_non_admins', 1);
    }

    /**
     * Enqueues custom login page styles.
     */
    public function login_styles(): void
    {
        wp_enqueue_style(
            'cubo-login',
            CUBO_CORE_URL . 'assets/css/login.css',
            [],
            CUBO_CORE_VERSION
        );
    }

    /**
     * Sets the login logo link to the site homepage.
     *
     * @return string
     */
    public function login_logo_url(): string
    {
        return home_url();
    }

    /**
     * Sets the login logo title attribute to the site name.
     *
     * @return string
     */
    public function login_logo_title(): string
    {
        return get_bloginfo('name');
    }

    /**
     * Redirects users to the WP dashboard after login.
     *
     * @param  string           $redirect_to           Requested redirect destination.
     * @param  string           $requested_redirect_to Original requested redirect.
     * @param  \WP_User|\WP_Error $user                 Authenticated user or error.
     * @return string
     */
    public function after_login_redirect(string $redirect_to, string $requested_redirect_to, $user): string
    {
        if ($user instanceof \WP_User) {
            return admin_url();
        }
        return $redirect_to;
    }

    /**
     * Redirects users to the login page after logout.
     *
     * @param  string $redirect_to           Requested redirect destination.
     * @param  string $requested_redirect_to Original requested redirect.
     * @param  \WP_User $user                Logged-out user.
     * @return string
     */
    public function after_logout_redirect(string $redirect_to, string $requested_redirect_to, \WP_User $user): string
    {
        return wp_login_url();
    }

    /**
     * Disables login by email address — username only.
     *
     * @param  \WP_User|\WP_Error|null $user     Authenticated user.
     * @param  string                  $username  Login input.
     * @param  string                  $password  Password input.
     * @return \WP_User|\WP_Error|null
     */
    public function disable_email_login($user, string $username, string $password)
    {
        if (is_email($username)) {
            return new \WP_Error(
                'email_login_disabled',
                __('Login by email is disabled. Please use your username.', 'cubo-core')
            );
        }
        return $user;
    }

    /**
     * Hides the admin bar for non-administrator users.
     */
    public function hide_admin_bar_for_non_admins(): void
    {
        if (! current_user_can('manage_options')) {
            show_admin_bar(false);
        }
    }

    /**
     * Replaces "Howdy, Username" in the admin bar with just the display name.
     *
     * @param \WP_Admin_Bar $admin_bar The admin bar instance.
     */
    public function remove_howdy(\WP_Admin_Bar $admin_bar): void
    {
        $user = wp_get_current_user();
        if (! $user->exists()) {
            return;
        }
        $admin_bar->add_menu([
            'id'    => 'my-account',
            'title' => $user->display_name,
        ]);
    }

    /**
     * Replaces the default WP admin footer text.
     *
     * @return string
     */
    public function custom_footer_text(): string
    {
        return esc_html__('Cubo', 'cubo-core');
    }

    /**
     * Removes the admin color scheme picker from the profile page.
     */
    public function remove_color_scheme_picker(): void
    {
        remove_action('admin_color_scheme_picker', 'wp_color_scheme_settings');
    }

    /**
     * Hides the core update nag from non-administrator users.
     */
    public function hide_update_nag_for_non_admins(): void
    {
        if (! current_user_can('update_core')) {
            remove_action('admin_notices', 'update_nag', 3);
        }
    }
}
