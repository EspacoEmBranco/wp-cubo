<?php

namespace Cubo\Core\Media;

use Cubo\Core\Loader;

/**
 * Disables big image scaling and removes unused intermediate image sizes.
 */
class Media
{
    /**
     * Registers all hooks with the loader.
     *
     * @param Loader $loader The plugin hook loader.
     */
    public function register(Loader $loader): void
    {
        $loader->add_filter('big_image_size_threshold', $this, 'disable_big_image_scaling');
        $loader->add_filter('intermediate_image_sizes_advanced', $this, 'remove_unused_image_sizes');
    }

    /**
     * Disables WP's automatic downscaling of large images on upload.
     *
     * @return false
     */
    public function disable_big_image_scaling(): bool
    {
        return false;
    }

    /**
     * Removes intermediate image sizes that are not used by Cubo.
     *
     * @param  array $sizes Registered intermediate sizes to generate.
     * @return array
     */
    public function remove_unused_image_sizes(array $sizes): array
    {
        unset($sizes['medium_large'], $sizes['1536x1536'], $sizes['2048x2048']);
        return $sizes;
    }
}
