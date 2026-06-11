<?php

namespace Cubo\Core\Cleanup;

use Cubo\Core\Loader;

/**
 * Performance optimizations: heartbeat, revisions, assets, block styles.
 *
 * Note: DISABLE_WP_CRON must be set in wp-config.php — it is checked before
 * plugins load and cannot be defined from a plugin. Add this to wp-config.php:
 * define( 'DISABLE_WP_CRON', true );
 * Then set up a server cron: * / 5 * * * * php /var/www/html/wp-cron.php
 */
class Performance
{
    /**
     * Registers all hooks with the loader.
     *
     * @param Loader $loader The plugin hook loader.
     */
    public function register(Loader $loader): void
    {
        $loader->add_filter('heartbeat_settings', $this, 'throttle_heartbeat');
        $loader->add_filter('wp_revisions_to_keep', $this, 'limit_revisions', 10, 2);
        $loader->add_filter('autosave_interval', $this, 'increase_autosave_interval');
        $loader->add_filter('style_loader_src', $this, 'remove_version_query_string');
        $loader->add_filter('script_loader_src', $this, 'remove_version_query_string');
        $loader->add_action('wp_enqueue_scripts', $this, 'dequeue_unnecessary_assets', 100);
    }

    /**
     * Reduces the Heartbeat API interval to 60 seconds.
     *
     * @param  array $settings Heartbeat settings.
     * @return array
     */
    public function throttle_heartbeat(array $settings): array
    {
        $settings['interval'] = 60;
        return $settings;
    }

    /**
     * Limits post revisions to 5 per post.
     *
     * @param  int      $num  Current revision limit.
     * @param  \WP_Post $post The post being revised.
     * @return int
     */
    public function limit_revisions(int $num, \WP_Post $post): int
    {
        return 5;
    }

    /**
     * Increases the autosave interval to 5 minutes.
     *
     * @return int Interval in seconds.
     */
    public function increase_autosave_interval(): int
    {
        return 300;
    }

    /**
     * Strips version query strings from asset URLs for better cache performance.
     *
     * @param  string $src Asset URL.
     * @return string
     */
    public function remove_version_query_string(string $src): string
    {
        if (str_contains($src, '?ver=')) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }

    /**
     * Dequeues scripts and styles not needed on the frontend.
     */
    public function dequeue_unnecessary_assets(): void
    {
        // wp-embed.min.js — not needed since embeds are disabled
        wp_deregister_script('wp-embed');

        // Dashicons — only needed for logged-in users
        if (! is_user_logged_in()) {
            wp_deregister_style('dashicons');
        }

        // Default block styles — revisit when theme design is finalised
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');

        // Classic theme styles — not needed with a block theme
        wp_dequeue_style('classic-theme-styles');
        wp_dequeue_style('global-styles');
    }
}
