<?php

namespace Cubo\Core\Base;

/**
 * Abstract base class for Cubo REST API controllers.
 *
 * Feature plugins extend this and implement register_routes().
 * All Cubo endpoints share the 'cubo/v1' namespace.
 *
 * Example usage in a feature plugin:
 *   class Projects_Controller extends Base_REST_Controller {
 *       protected function get_rest_base(): string { return 'projects'; }
 *       public function register_routes(): void {
 *           register_rest_route( $this->namespace, '/' . $this->rest_base, [ ... ] );
 *       }
 *   }
 *   add_action( 'rest_api_init', [ new Projects_Controller(), 'register_routes' ] );
 */
abstract class Base_REST_Controller extends \WP_REST_Controller
{
    /**
     * API namespace for all Cubo endpoints.
     *
     * @var string
     */
    protected $namespace = 'cubo/v1';

    /**
     * Initializes the controller and sets the rest_base from the subclass.
     */
    public function __construct()
    {
        $this->rest_base = $this->get_rest_base();
    }

    /**
     * Returns the REST base path for this controller (e.g. 'projects').
     *
     * @return string
     */
    abstract protected function get_rest_base(): string;

    /**
     * Registers REST routes. Hooked to rest_api_init.
     * Subclasses must override this method.
     */
    public function register_routes(): void {}

    /**
     * Default permission check — requires the user to be logged in.
     *
     * @param  \WP_REST_Request $request Incoming request.
     * @return bool|\WP_Error
     */
    public function check_permission(\WP_REST_Request $request): bool|\WP_Error
    {
        if (! is_user_logged_in()) {
            return new \WP_Error(
                'rest_forbidden',
                __('You must be logged in to access this endpoint.', 'cubo-core'),
                [ 'status' => 401 ]
            );
        }
        return true;
    }
}
