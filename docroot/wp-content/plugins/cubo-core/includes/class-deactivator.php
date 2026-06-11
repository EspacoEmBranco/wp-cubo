<?php

namespace Cubo\Core;

/**
 * Handles plugin deactivation.
 */
class Deactivator
{
    /**
     * Runs on plugin deactivation.
     */
    public static function deactivate(): void
    {
        flush_rewrite_rules(false);
    }
}
