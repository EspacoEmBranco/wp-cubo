<?php

namespace Cubo\Core\Content;

use Cubo\Core\Loader;

/**
 * Disables comments, unused taxonomies, post formats, and registers the Cubo block category.
 */
class Content
{
    /**
     * Registers all hooks with the loader.
     *
     * @param Loader $loader The plugin hook loader.
     */
    public function register(Loader $loader): void
    {
        $loader->add_action('init', $this, 'disable_comments');
        $loader->add_action('init', $this, 'disable_post_formats');
        $loader->add_filter('block_categories_all', $this, 'register_cubo_block_category', 10, 2);
        $loader->add_filter('allowed_block_types_all', $this, 'restrict_block_types', 10, 2);
        $loader->add_action('enqueue_block_editor_assets', $this, 'enqueue_editor_styles');
    }

    /**
     * Removes comment and trackback support from all registered post types.
     */
    public function disable_comments(): void
    {
        foreach (get_post_types() as $post_type) {
            if (post_type_supports($post_type, 'comments')) {
                remove_post_type_support($post_type, 'comments');
                remove_post_type_support($post_type, 'trackbacks');
            }
        }
    }

    /**
     * Removes post format support from the theme.
     */
    public function disable_post_formats(): void
    {
        remove_theme_support('post-formats');
    }

    /**
     * Prepends the Cubo block category to the block inserter.
     *
     * @param  array                    $categories Block categories.
     * @param  \WP_Block_Editor_Context $context    Current editor context.
     * @return array
     */
    public function register_cubo_block_category(array $categories, \WP_Block_Editor_Context $context): array
    {
        array_unshift($categories, [
            'slug'  => 'cubo',
            'title' => __('Cubo', 'cubo-core'),
            'icon'  => 'grid-view',
        ]);
        return $categories;
    }

    /**
     * Controls allowed block types per post type.
     * Returns true (all blocks allowed) by default — feature plugins narrow this further.
     *
     * @param  bool|array               $allowed_blocks Allowed block type slugs, or true for all.
     * @param  \WP_Block_Editor_Context $context        Current editor context.
     * @return bool|array
     */
    public function restrict_block_types($allowed_blocks, \WP_Block_Editor_Context $context)
    {
        return true;
    }

    /**
     * Enqueues custom styles for the block editor.
     * Will be populated once design tokens are finalized.
     */
    public function enqueue_editor_styles(): void
    {
        // Editor styles will be added here when the design system is ready.
    }
}
