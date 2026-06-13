<?php

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$jobs = get_posts([
    'post_type'   => 'cubo_job',
    'post_status' => 'any',
    'numberposts' => -1,
    'fields'      => 'ids',
]);

foreach ($jobs as $job_id) {
    wp_delete_post($job_id, true);
}
