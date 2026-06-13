<?php

namespace Cubo\Work;

/**
 * Handles plugin activation.
 */
class Activator
{
    /**
     * Runs on plugin activation. Flushes rewrite rules so cubo_job URLs resolve.
     */
    public static function activate(): void
    {
        flush_rewrite_rules(false);
    }
}
