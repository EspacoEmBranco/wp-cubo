<?php

namespace Cubo\Core\Cleanup;

use Cubo\Core\Loader;

/**
 * Removes junk from the <head>, version strings, emoji scripts, and embed code.
 */
class Head
{
    /**
     * Registers all hooks with the loader.
     *
     * @param Loader $loader The plugin hook loader.
     */
    public function register(Loader $loader): void
    {
        $loader->add_action('init', $this, 'remove_head_links');
        $loader->add_action('init', $this, 'disable_emojis');
        $loader->add_action('init', $this, 'disable_embeds');
        $loader->add_filter('the_generator', $this, 'remove_version_string');
    }

    /**
     * Removes unnecessary link tags from <head>.
     */
    public function remove_head_links(): void
    {
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('wp_head', 'wp_generator');
        remove_action('template_redirect', 'rest_output_link_header', 11);
    }

    /**
     * Removes all emoji-related scripts, styles, and DNS prefetch hints.
     */
    public function disable_emojis(): void
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        add_filter('tiny_mce_plugins', [ $this, 'remove_emoji_tinymce_plugin' ]);
        add_filter('wp_resource_hints', [ $this, 'remove_emoji_dns_prefetch' ], 10, 2);
    }

    /**
     * Disables oEmbed discovery links, host JS, and REST route.
     */
    public function disable_embeds(): void
    {
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
        remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
        remove_action('rest_api_init', 'wp_oembed_register_route');
        add_filter('embed_oembed_discover', '__return_false');
        add_filter('rewrite_rules_array', [ $this, 'remove_embed_rewrite_rules' ]);
    }

    /**
     * Strips the WP version string from all generator outputs.
     *
     * @return string
     */
    public function remove_version_string(): string
    {
        return '';
    }

    /**
     * Removes the wpemoji plugin from TinyMCE.
     *
     * @param  array $plugins TinyMCE plugins list.
     * @return array
     */
    public function remove_emoji_tinymce_plugin(array $plugins): array
    {
        return array_diff($plugins, [ 'wpemoji' ]);
    }

    /**
     * Strips the emoji DNS prefetch hint from resource hints.
     *
     * @param  array  $urls          Resource hint URLs.
     * @param  string $relation_type Hint type (dns-prefetch, preconnect, etc.).
     * @return array
     */
    public function remove_emoji_dns_prefetch(array $urls, string $relation_type): array
    {
        if ('dns-prefetch' === $relation_type) {
            $urls = array_filter($urls, fn ($url) => ! str_contains((string) $url, 'twemoji'));
        }
        return $urls;
    }

    /**
     * Removes embed rewrite rules.
     *
     * @param  array $rules Registered rewrite rules.
     * @return array
     */
    public function remove_embed_rewrite_rules(array $rules): array
    {
        return array_filter($rules, fn ($key) => ! str_contains($key, 'embed'), ARRAY_FILTER_USE_KEY);
    }
}
