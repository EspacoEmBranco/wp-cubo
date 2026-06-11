<?php

namespace Cubo\Core;

/**
 * Collects and registers all actions and filters for the plugin.
 */
class Loader
{
    /**
     * @var array $actions Registered actions.
     */
    private array $actions = [];

    /**
     * @var array $filters Registered filters.
     */
    private array $filters = [];

    /**
     * Adds an action to the collection.
     *
     * @param string $hook      The WordPress action hook name.
     * @param object $component The object containing the callback.
     * @param string $callback  The method name on the component.
     * @param int    $priority  Hook priority.
     * @param int    $args      Number of accepted arguments.
     */
    public function add_action(string $hook, object $component, string $callback, int $priority = 10, int $args = 1): void
    {
        $this->actions[] = compact('hook', 'component', 'callback', 'priority', 'args');
    }

    /**
     * Adds a filter to the collection.
     *
     * @param string $hook      The WordPress filter hook name.
     * @param object $component The object containing the callback.
     * @param string $callback  The method name on the component.
     * @param int    $priority  Hook priority.
     * @param int    $args      Number of accepted arguments.
     */
    public function add_filter(string $hook, object $component, string $callback, int $priority = 10, int $args = 1): void
    {
        $this->filters[] = compact('hook', 'component', 'callback', 'priority', 'args');
    }

    /**
     * Registers all collected actions and filters with WordPress.
     */
    public function run(): void
    {
        foreach ($this->actions as $action) {
            add_action($action['hook'], [ $action['component'], $action['callback'] ], $action['priority'], $action['args']);
        }

        foreach ($this->filters as $filter) {
            add_filter($filter['hook'], [ $filter['component'], $filter['callback'] ], $filter['priority'], $filter['args']);
        }
    }
}
