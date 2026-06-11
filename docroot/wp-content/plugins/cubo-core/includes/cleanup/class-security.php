<?php

namespace Cubo\Core\Cleanup;

use Cubo\Core\Loader;

/**
 * Hardens WordPress security: REST auth, user enumeration, application passwords, headers, file editing.
 */
class Security
{
    /**
     * Registers all hooks with the loader.
     *
     * @param Loader $loader The plugin hook loader.
     */
    public function register(Loader $loader): void
    {
        $loader->add_filter('rest_authentication_errors', $this, 'restrict_rest_api');
        $loader->add_action('template_redirect', $this, 'prevent_user_enumeration');
        $loader->add_filter('wp_is_application_passwords_available', $this, 'disable_application_passwords');
        $loader->add_filter('login_errors', $this, 'generic_login_errors');
        $loader->add_action('send_headers', $this, 'add_security_headers');
        $loader->add_action('admin_init', $this, 'disable_file_editing');
    }

    /**
     * Restricts the REST API to authenticated users only.
     *
     * @param  \WP_Error|null|true $errors Existing authentication errors.
     * @return \WP_Error|null|true
     */
    public function restrict_rest_api($errors)
    {
        if (! empty($errors)) {
            return $errors;
        }

        if (! is_user_logged_in()) {
            return new \WP_Error(
                'rest_not_logged_in',
                __('You must be logged in to use the REST API.', 'cubo-core'),
                [ 'status' => 401 ]
            );
        }

        return $errors;
    }

    /**
     * Prevents user enumeration via the ?author= query parameter.
     */
    public function prevent_user_enumeration(): void
    {
        if (isset($_GET['author']) && ! is_admin()) { // phpcs:ignore WordPress.Security.NonceVerification
            wp_safe_redirect(home_url(), 301);
            exit;
        }
    }

    /**
     * Disables application passwords.
     *
     * @return bool
     */
    public function disable_application_passwords(): bool
    {
        return false;
    }

    /**
     * Replaces specific WP login error messages with a generic one.
     *
     * @return string
     */
    public function generic_login_errors(): string
    {
        return __('Invalid credentials.', 'cubo-core');
    }

    /**
     * Adds security headers to every response.
     */
    public function add_security_headers(): void
    {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    }

    /**
     * Prevents file editing via the WP admin theme/plugin editors.
     */
    public function disable_file_editing(): void
    {
        if (! defined('DISALLOW_FILE_EDIT')) {
            define('DISALLOW_FILE_EDIT', true);
        }
    }
}
