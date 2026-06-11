<?php

namespace Cubo\Core;

/**
 * Handles plugin activation.
 */
class Activator
{
    /**
     * Runs on plugin activation.
     */
    public static function activate(): void
    {
        self::set_permalinks();
        self::remove_default_content();
    }

    /**
     * Sets the permalink structure to /%postname%/.
     */
    private static function set_permalinks(): void
    {
        update_option('permalink_structure', '/%postname%/');
        flush_rewrite_rules(false);
    }

    /**
     * Removes the default "Hello World" post and "Sample Page".
     */
    private static function remove_default_content(): void
    {
        $hello_world = get_page_by_path('hello-world', OBJECT, 'post');
        if ($hello_world) {
            wp_delete_post($hello_world->ID, true);
        }

        $sample_page = get_page_by_path('sample-page', OBJECT, 'page');
        if ($sample_page) {
            wp_delete_post($sample_page->ID, true);
        }
    }
}
