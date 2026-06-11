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
     * Instantiates all modules and registers their hooks with the loader.
     */
    private function define_hooks(): void
    {
        // Cleanup
        (new Cleanup\Feeds())->register($this->loader);
        (new Cleanup\Head())->register($this->loader);
        (new Cleanup\Security())->register($this->loader);
        (new Cleanup\Performance())->register($this->loader);

        // Admin
        (new Admin\Menu())->register($this->loader);
        (new Admin\Dashboard())->register($this->loader);
        (new Admin\Branding())->register($this->loader);

        // Assets
        (new Assets\Assets())->register($this->loader);

        // Content
        (new Content\Content())->register($this->loader);

        // Media
        (new Media\Media())->register($this->loader);

        // Mail
        (new Mail\Mail())->register($this->loader);

        // Users
        (new Users\Users())->register($this->loader);
    }

    /**
     * Runs the loader to register all hooks with wordpress.
     */
    public function run(): void
    {
        $this->loader->run();
    }
}
