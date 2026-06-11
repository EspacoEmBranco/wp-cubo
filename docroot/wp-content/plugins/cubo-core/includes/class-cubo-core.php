<?php

namespace Cubo\Core;

/**
 * Main plugin class. Instantiates all modules and registers their hooks.
 */

class Cubo_Core
{
    /**
     * @var Loader $loader Collects and registers all the hooks.
     */
    private Loader $loader;

    /**
     * Initializes the loader and wires all the module hooks.
     */
    public function __construct()
    {
        $this->loader = new Loader();
        $this->define_hooks();
    }

    /**
     * Registers all module hooks with the loader.
     * Each module is instantiated here an its hooks added.
     * Expand this method as modules are built.
     */
    private function define_hooks(): void
    {
        // Modules are registered here as they are built.
        // Example:
        // $cleanup = new Cleanup\Feeds();
        // $this->loader->add_action('init', $cleanup, 'disable_feeds');
    }

    /**
     * Runs the loader to register all hooks with wordpress.
     */
    public function run(): void
    {
        $this->loader->run();
    }
}
