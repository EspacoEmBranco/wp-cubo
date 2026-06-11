<?php

namespace Cubo\Core\Cleanup;

use Cubo\Core\Loader;

/**
 * Disables RSS/Atom feeds, XML-RPC, pingbacks, trackbacks, and the X-Pingback header.
 */
class Feeds
{
    /**
     * Registers all hooks with the loader.
     *
     * @param Loader $loader The plugin hook loader.
     */
    public function register(Loader $loader): void
    {
        $loader->add_action('do_feed', $this, 'disable_feeds', 1);
        $loader->add_action('do_feed_rdf', $this, 'disable_feeds', 1);
        $loader->add_action('do_feed_rss', $this, 'disable_feeds', 1);
        $loader->add_action('do_feed_rss2', $this, 'disable_feeds', 1);
        $loader->add_action('do_feed_atom', $this, 'disable_feeds', 1);
        $loader->add_action('do_feed_rss2_comments', $this, 'disable_feeds', 1);
        $loader->add_action('do_feed_atom_comments', $this, 'disable_feeds', 1);
        $loader->add_filter('xmlrpc_enabled', $this, 'disable_xmlrpc');
        $loader->add_filter('pings_open', $this, 'disable_pings');
        $loader->add_action('pre_ping', $this, 'disable_self_pings');
        $loader->add_filter('wp_headers', $this, 'remove_x_pingback_header');
    }

    /**
     * Returns a 404 for any feed request.
     */
    public function disable_feeds(): void
    {
        wp_die('', '', [ 'response' => 404 ]);
    }

    /**
     * Disables XML-RPC.
     *
     * @return bool
     */
    public function disable_xmlrpc(): bool
    {
        return false;
    }

    /**
     * Closes pingbacks and trackbacks on all posts.
     *
     * @return bool
     */
    public function disable_pings(): bool
    {
        return false;
    }

    /**
     * Removes own URLs from the pingback queue to prevent self-pings.
     *
     * @param array $links Discovered outbound links.
     */
    public function disable_self_pings(array &$links): void
    {
        $home = get_option('siteurl');
        $links = array_filter($links, fn ($link) => ! str_starts_with($link, $home));
    }

    /**
     * Removes the X-Pingback HTTP header.
     *
     * @param  array $headers Response headers.
     * @return array
     */
    public function remove_x_pingback_header(array $headers): array
    {
        unset($headers['X-Pingback']);
        return $headers;
    }
}
