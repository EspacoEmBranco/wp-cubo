<?php

namespace Cubo\Work\CPT;

use Cubo\Core\Base\Base_CPT;

/**
 * Registers the cubo_job custom post type.
 */
class Job_CPT extends Base_CPT
{
    /**
     * {@inheritDoc}
     */
    protected function get_post_type(): string
    {
        return 'cubo_job';
    }

    /**
     * {@inheritDoc}
     */
    protected function get_labels(): array
    {
        return [
            'name'               => __('Jobs', 'cubo-work'),
            'singular_name'      => __('Job', 'cubo-work'),
            'add_new'            => __('Add New', 'cubo-work'),
            'add_new_item'       => __('Add New Job', 'cubo-work'),
            'edit_item'          => __('Edit Job', 'cubo-work'),
            'new_item'           => __('New Job', 'cubo-work'),
            'view_item'          => __('View Job', 'cubo-work'),
            'search_items'       => __('Search Jobs', 'cubo-work'),
            'not_found'          => __('No jobs found.', 'cubo-work'),
            'not_found_in_trash' => __('No jobs found in trash.', 'cubo-work'),
            'menu_name'          => __('Jobs', 'cubo-work'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function get_args(): array
    {
        return [
            'supports' => ['title', 'editor', 'revisions'],
        ];
    }
}
