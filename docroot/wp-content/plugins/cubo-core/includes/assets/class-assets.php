<?php

namespace Cubo\Core\Assets;

use Cubo\Core\Loader;

/**
 * Enqueues global design tokens, fonts, and shared JS utilities.
 */
class Assets
{
    /**
     * Registers all hooks with the loader.
     *
     * @param Loader $loader The plugin hook loader.
     */
    public function register(Loader $loader): void
    {
        $loader->add_action('wp_enqueue_scripts', $this, 'enqueue_frontend_assets');
        $loader->add_action('admin_enqueue_scripts', $this, 'enqueue_admin_assets');
        $loader->add_action('wp_head', $this, 'add_resource_hints', 2);
    }

    /**
     * Enqueues design tokens and fonts on the frontend.
     */
    public function enqueue_frontend_assets(): void
    {
        wp_enqueue_style(
            'cubo-tokens',
            CUBO_CORE_URL . 'assets/css/tokens.css',
            [],
            CUBO_CORE_VERSION
        );
        // Self-hosted fonts will be enqueued here once the font is decided.
    }

    /**
     * Enqueues design tokens in the admin.
     */
    public function enqueue_admin_assets(): void
    {
        wp_enqueue_style(
            'cubo-tokens',
            CUBO_CORE_URL . 'assets/css/tokens.css',
            [],
            CUBO_CORE_VERSION
        );
    }

    /**
     * Outputs preconnect and dns-prefetch hints for external resources.
     * Populate as external dependencies (fonts, CDNs) are added.
     */
    public function add_resource_hints(): void
    {
        // Example:
        // echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    }
}
