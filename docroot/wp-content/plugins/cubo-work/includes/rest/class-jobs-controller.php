<?php

namespace Cubo\Work\REST;

use Cubo\Core\Base\Base_REST_Controller;

/**
 * REST controller for cubo_job endpoints under /cubo/v1/jobs.
 */
class Jobs_Controller extends Base_REST_Controller
{
    /**
     * {@inheritDoc}
     */
    protected function get_rest_base(): string
    {
        return 'jobs';
    }

    /**
     * {@inheritDoc}
     */
    public function register_routes(): void
    {
        // TODO: GET /cubo/v1/jobs, GET /cubo/v1/jobs/{id}, POST /cubo/v1/jobs, PATCH /cubo/v1/jobs/{id}.
    }
}
