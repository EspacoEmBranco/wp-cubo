<?php

namespace Cubo\Core\Base;

/**
 * Abstract base class for Cubo custom post types.
 *
 * Feature plugins extend this class and implement the abstract methods.
 * Call register() on the init hook to register the post type with WordPress.
 *
 * Example usage in a feature plugin:
 *   class Project_CPT extends Base_CPT {
 *       protected function get_post_type(): string { return 'cubo_project'; }
 *       protected function get_labels(): array { ... }
 *       protected function get_args(): array { ... }
 *   }
 *   add_action( 'init', [ new Project_CPT(), 'register' ] );
 */
abstract class Base_CPT
{
    /**
     * Returns the post type key (e.g. 'cubo_project').
     *
     * @return string
     */
    abstract protected function get_post_type(): string;

    /**
     * Returns the labels array for register_post_type().
     *
     * @return array
     */
    abstract protected function get_labels(): array;

    /**
     * Returns post type arguments that override or extend the defaults.
     *
     * @return array
     */
    abstract protected function get_args(): array;

    /**
     * Registers the custom post type with WordPress.
     * Merges defaults with the subclass-defined args.
     */
    public function register(): void
    {
        register_post_type(
            $this->get_post_type(),
            array_merge($this->get_defaults(), $this->get_args())
        );
    }

    /**
     * Returns default CPT arguments shared across all Cubo post types.
     *
     * @return array
     */
    private function get_defaults(): array
    {
        return [
            'public'             => false,
            'show_ui'            => true,
            'show_in_rest'       => true,
            'show_in_menu'       => false, // Feature plugins attach to the Cubo submenu manually.
            'supports'           => [ 'title', 'editor', 'revisions' ],
            'labels'             => $this->get_labels(),
        ];
    }
}
