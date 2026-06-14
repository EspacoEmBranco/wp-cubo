<?php

namespace Cubo\Work\Meta;

/**
 * Registers and handles meta fields for the cubo_job post type.
 */
class Job_Meta
{
    /**
     * Allowed values for the stage field.
     */
    private const STAGES = ['Applied', 'Screening', 'Interview', 'Offer', 'Rejected', 'Withdrawn'];

    /**
     * Allowed values for the outcome field.
     */
    private const OUTCOMES = ['Pending', 'Positive', 'Negative'];

    /**
     * Allowed values for the source field.
     */
    private const SOURCES = ['LinkedIn', 'Direct', 'Referral', 'Research', 'Other'];

    /**
     * Registers hooks for meta field registration, meta boxes, and saving.
     */
    public function register(): void
    {
        add_action('init', [$this, 'register_fields']);
        add_action('add_meta_boxes', [$this, 'register_meta_boxes']);
        add_action('save_post_cubo_job', [$this, 'save']);
    }

    /**
     * Registers all meta fields for the cubo_job post type.
     *
     * Fields are exposed via the REST API (show_in_rest: true) for
     * consumption by blocks and the Jobs REST controller.
     */
    public function register_fields(): void
    {
        $common = [
            'type'          => 'string',
            'single'        => true,
            'show_in_rest'  => true,
            'auth_callback' => fn () => current_user_can('edit_posts'),
        ];

        register_post_meta('cubo_job', '_cubo_company', array_merge($common, [
            'sanitize_callback' => 'sanitize_text_field',
        ]));

        register_post_meta('cubo_job', '_cubo_role', array_merge($common, [
            'sanitize_callback' => 'sanitize_text_field',
        ]));

        register_post_meta('cubo_job', '_cubo_salary', array_merge($common, [
            'sanitize_callback' => 'sanitize_text_field',
        ]));

        register_post_meta('cubo_job', '_cubo_location', array_merge($common, [
            'sanitize_callback' => 'sanitize_text_field',
        ]));

        register_post_meta('cubo_job', '_cubo_job_url', array_merge($common, [
            'sanitize_callback' => 'esc_url_raw',
        ]));

        register_post_meta('cubo_job', '_cubo_source', array_merge($common, [
            'sanitize_callback' => 'sanitize_text_field',
        ]));

        register_post_meta('cubo_job', '_cubo_date_applied', array_merge($common, [
            'sanitize_callback' => 'sanitize_text_field',
        ]));

        register_post_meta('cubo_job', '_cubo_stage', array_merge($common, [
            'sanitize_callback' => 'sanitize_text_field',
        ]));

        register_post_meta('cubo_job', '_cubo_outcome', array_merge($common, [
            'sanitize_callback' => 'sanitize_text_field',
        ]));

        // Contacts are stored as a JSON string. The sanitize callback validates
        // the structure and re-encodes to ensure consistent formatting.
        register_post_meta('cubo_job', '_cubo_contacts', array_merge($common, [
            'sanitize_callback' => function (string $value): string {
                $decoded = json_decode($value, true);
                return is_array($decoded) ? wp_json_encode($decoded) : '[]';
            },
        ]));
    }

    /**
     * Registers the three meta boxes for the cubo_job post editor.
     */
    public function register_meta_boxes(): void
    {
        add_meta_box(
            'cubo-job-details',
            __('Job Details', 'cubo-work'),
            [$this, 'render_details_box'],
            'cubo_job',
            'normal',
            'high'
        );

        add_meta_box(
            'cubo-job-pipeline',
            __('Pipeline', 'cubo-work'),
            [$this, 'render_pipeline_box'],
            'cubo_job',
            'normal',
            'high'
        );

        add_meta_box(
            'cubo-job-contacts',
            __('Contacts', 'cubo-work'),
            [$this, 'render_contacts_box'],
            'cubo_job',
            'normal',
            'default'
        );
    }

    /**
     * Renders the Job Details meta box.
     *
     * Also outputs the nonce field that covers all three meta boxes,
     * since they all submit within the same post edit form.
     *
     * @param \WP_Post $post The current post object.
     */
    public function render_details_box(\WP_Post $post): void
    {
        wp_nonce_field('cubo_job_meta_save', 'cubo_job_meta_nonce');

        $company  = (string) get_post_meta($post->ID, '_cubo_company', true);
        $role     = (string) get_post_meta($post->ID, '_cubo_role', true);
        $salary   = (string) get_post_meta($post->ID, '_cubo_salary', true);
        $location = (string) get_post_meta($post->ID, '_cubo_location', true);
        $job_url  = (string) get_post_meta($post->ID, '_cubo_job_url', true);
        $source   = (string) get_post_meta($post->ID, '_cubo_source', true);
        ?>
            <table class="cubo-meta-table">
                <tr>
                    <th><label for="cubo_company"><?php esc_html_e('Company', 'cubo-work'); ?></label></th>
                    <td><input type="text" id="cubo_company" name="cubo_company" value="<?php echo esc_attr($company); ?>"></td>
                </tr>
                <tr>
                    <th><label for="cubo_role"><?php esc_html_e('Role', 'cubo-work'); ?></label></th>
                    <td><input type="text" id="cubo_role" name="cubo_role" value="<?php echo esc_attr($role); ?>"></td>
                </tr>
                <tr>
                    <th><label for="cubo_location"><?php esc_html_e('Location', 'cubo-work'); ?></label></th>
                    <td><input type="text" id="cubo_location" name="cubo_location" value="<?php echo esc_attr($location); ?>"></td>
                </tr>
                <tr>
                    <th><label for="cubo_job_url"><?php esc_html_e('Job URL', 'cubo-work'); ?></label></th>
                    <td><input type="url" id="cubo_job_url" name="cubo_job_url" value="<?php echo esc_attr($job_url); ?>"></td>
                </tr>
                <tr>
                    <th><label for="cubo_salary"><?php esc_html_e('Salary Range', 'cubo-work'); ?></label></th>
                    <td><input type="text" id="cubo_salary" name="cubo_salary" value="<?php echo esc_attr($salary); ?>"></td>
                </tr>
                <tr>
                    <th><label for="cubo_source"><?php esc_html_e('Source', 'cubo-work'); ?></label></th>
                    <td>
                        <select id="cubo_source" name="cubo_source">
                            <option value=""><?php esc_html_e('— Select —', 'cubo-work'); ?></option>
                            <?php foreach (self::SOURCES as $option) : ?>
                                <option value="<?php echo esc_attr($option); ?>" <?php selected($source, $option); ?>>
                                    <?php echo esc_html($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <?php
    }

    /**
     * Renders the Pipeline meta box.
     *
     * @param \WP_Post $post The current post object.
     */
    public function render_pipeline_box(\WP_Post $post): void
    {
        $stage        = (string) get_post_meta($post->ID, '_cubo_stage', true);
        $outcome      = (string) get_post_meta($post->ID, '_cubo_outcome', true);
        $date_applied = (string) get_post_meta($post->ID, '_cubo_date_applied', true);
        ?>
            <table class="cubo-meta-table">
                <tr>
                    <th><label for="cubo_stage"><?php esc_html_e('Stage', 'cubo-work'); ?></label></th>
                    <td>
                        <select id="cubo_stage" name="cubo_stage">
                            <option value=""><?php esc_html_e('— Select —', 'cubo-work'); ?></option>
                            <?php foreach (self::STAGES as $option) : ?>
                                <option value="<?php echo esc_attr($option); ?>" <?php selected($stage, $option); ?>>
                                    <?php echo esc_html($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="cubo_outcome"><?php esc_html_e('Outcome', 'cubo-work'); ?></label></th>
                    <td>
                        <select id="cubo_outcome" name="cubo_outcome">
                            <option value=""><?php esc_html_e('— Select —', 'cubo-work'); ?></option>
                            <?php foreach (self::OUTCOMES as $option) : ?>
                                <option value="<?php echo esc_attr($option); ?>" <?php selected($outcome, $option); ?>>
                                    <?php echo esc_html($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="cubo_date_applied"><?php esc_html_e('Date Applied', 'cubo-work'); ?></label></th>
                    <td><input type="date" id="cubo_date_applied" name="cubo_date_applied" value="<?php echo esc_attr($date_applied); ?>"></td>
                </tr>
            </table>
            <?php
    }

    /**
     * Renders the Contacts meta box.
     *
     * Contacts are stored as a JSON string (array of {name, role, email, notes}).
     * A structured UI will replace this textarea in a future iteration.
     *
     * @param \WP_Post $post The current post object.
     */
    public function render_contacts_box(\WP_Post $post): void
    {
        $contacts = (string) get_post_meta($post->ID, '_cubo_contacts', true);
        ?>
            <p class="description">
                <?php esc_html_e('JSON array — e.g. [{"name":"","role":"","email":"","notes":""}]', 'cubo-work'); ?>
            </p>
            <textarea id="cubo_contacts" name="cubo_contacts" rows="6" style="width:100%;"><?php echo esc_textarea($contacts); ?></textarea>
            <?php
    }

    /**
     * Saves all meta fields for the cubo_job post type.
     *
     * Verifies nonce and capability, skips autosave, sanitizes and validates
     * each field, then auto-generates the post title from role and company.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save(int $post_id): void
    {
        if (
            ! isset($_POST['cubo_job_meta_nonce']) ||
            ! wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['cubo_job_meta_nonce'])),
                'cubo_job_meta_save'
            )
        ) {
            return;
        }

        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Plain text fields — no enum constraint.
        foreach (['cubo_company', 'cubo_role', 'cubo_salary', 'cubo_location', 'cubo_date_applied'] as $field) {
            if (isset($_POST[$field])) {
                update_post_meta(
                    $post_id,
                    '_' . $field,
                    sanitize_text_field(wp_unslash($_POST[$field]))
                );
            }
        }

        // URL field.
        if (isset($_POST['cubo_job_url'])) {
            update_post_meta(
                $post_id,
                '_cubo_job_url',
                esc_url_raw(wp_unslash($_POST['cubo_job_url']))
            );
        }

        // Enum fields — only save if the value is in the allowed list.
        $enums = [
            'cubo_stage'   => self::STAGES,
            'cubo_outcome' => self::OUTCOMES,
            'cubo_source'  => self::SOURCES,
        ];
        foreach ($enums as $field => $allowed) {
            if (isset($_POST[$field])) {
                $value = sanitize_text_field(wp_unslash($_POST[$field]));
                if (in_array($value, $allowed, true)) {
                    update_post_meta($post_id, '_' . $field, $value);
                }
            }
        }

        // Contacts — only save if the value is empty or valid JSON.
        if (isset($_POST['cubo_contacts'])) {
            $contacts = sanitize_textarea_field(wp_unslash($_POST['cubo_contacts']));
            if ($contacts === '' || json_decode($contacts) !== null) {
                update_post_meta($post_id, '_cubo_contacts', $contacts);
            }
        }

        // Auto-generate post title as "Role at Company".
        // Remove and re-add the hook to prevent an infinite loop when
        // wp_update_post triggers save_post_cubo_job a second time.
        $role    = (string) get_post_meta($post_id, '_cubo_role', true);
        $company = (string) get_post_meta($post_id, '_cubo_company', true);
        if ($role && $company) {
            $title = $role . ' at ' . $company;
            remove_action('save_post_cubo_job', [$this, 'save']);
            wp_update_post([
                'ID'         => $post_id,
                'post_title' => $title,
                'post_name'  => sanitize_title($title),
            ]);
            add_action('save_post_cubo_job', [$this, 'save']);
        }
    }
}
