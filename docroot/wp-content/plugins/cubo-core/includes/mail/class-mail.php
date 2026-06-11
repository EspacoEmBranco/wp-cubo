<?php

namespace Cubo\Core\Mail;

use Cubo\Core\Loader;

/**
 * Configures mail sender, SMTP via PHPMailer, and disables certain WP notification emails.
 *
 * SMTP credentials are read from wp-config.php constants:
 *   CUBO_MAIL_HOST, CUBO_MAIL_PORT, CUBO_MAIL_USERNAME,
 *   CUBO_MAIL_PASSWORD, CUBO_MAIL_ENCRYPTION (tls|ssl),
 *   CUBO_MAIL_FROM_EMAIL, CUBO_MAIL_FROM_NAME
 */
class Mail
{
    /**
     * Registers all hooks with the loader.
     *
     * @param Loader $loader The plugin hook loader.
     */
    public function register(Loader $loader): void
    {
        $loader->add_action('phpmailer_init', $this, 'configure_smtp');
        $loader->add_filter('wp_mail_from', $this, 'custom_sender_email');
        $loader->add_filter('wp_mail_from_name', $this, 'custom_sender_name');

        // Disable notification emails not relevant to a personal site
        add_filter('send_password_change_email', '__return_false');
        add_filter('send_email_change_email', '__return_false');
        add_filter('wp_send_new_user_notification_to_user', '__return_false');
    }

    /**
     * Configures PHPMailer to use SMTP if credentials are defined.
     *
     * @param \PHPMailer\PHPMailer\PHPMailer $phpmailer PHPMailer instance.
     */
    public function configure_smtp($phpmailer): void
    {
        if (! defined('CUBO_MAIL_HOST')) {
            return;
        }

        $phpmailer->isSMTP();
        $phpmailer->Host       = CUBO_MAIL_HOST;
        $phpmailer->Port       = defined('CUBO_MAIL_PORT') ? CUBO_MAIL_PORT : 587;
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Username   = defined('CUBO_MAIL_USERNAME') ? CUBO_MAIL_USERNAME : '';
        $phpmailer->Password   = defined('CUBO_MAIL_PASSWORD') ? CUBO_MAIL_PASSWORD : '';
        $phpmailer->SMTPSecure = defined('CUBO_MAIL_ENCRYPTION') ? CUBO_MAIL_ENCRYPTION : 'tls';
    }

    /**
     * Sets the sender email address.
     *
     * @param  string $email Default sender email.
     * @return string
     */
    public function custom_sender_email(string $email): string
    {
        return defined('CUBO_MAIL_FROM_EMAIL') ? CUBO_MAIL_FROM_EMAIL : $email;
    }

    /**
     * Sets the sender display name.
     *
     * @param  string $name Default sender name.
     * @return string
     */
    public function custom_sender_name(string $name): string
    {
        return defined('CUBO_MAIL_FROM_NAME') ? CUBO_MAIL_FROM_NAME : $name;
    }
}
