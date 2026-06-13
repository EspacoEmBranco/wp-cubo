<?php

namespace Cubo\Work;

/**
 * Main plugin class. Instantiates all modules and registers their hooks.
 */
class Cubo_Work
{
    /**
     * Boots all modules.
     */
    public function run(): void
    {
        add_action('init', [(new CPT\Job_CPT()), 'register']);

        (new Meta\Job_Meta())->register();
        (new Admin\Job_Admin())->register();

        add_action('rest_api_init', [(new REST\Jobs_Controller()), 'register_routes']);
    }
}
