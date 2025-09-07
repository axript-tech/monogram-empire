<?php
// File: php/config/mailer.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// **IMPORTANT**: You must download PHPMailer and place it in the 'php/lib/' directory.
// You can get it from GitHub: https://github.com/PHPMailer/PHPMailer
include_once 'email_template.php';

require_once 'Exception.php';
require_once 'PHPMailer.php';
require_once 'SMTP.php';

/**
 * Sends an email using the site's template and SMTP settings.
 *
 * @param mysqli $conn The database connection object.
 * @param string $to_email Recipient's email.
 * @param string $to_name Recipient's name.
 * @param string $subject Email subject.
 * @param string $email_title The headline to show inside the email template.
 * @param string $email_content The core HTML content for the email.
 * @param string|null $reply_to_email Optional reply-to email.
 * @param string|null $reply_to_name Optional reply-to name.
 * @return bool True on success, false on failure.
 */
function send_email($conn, $to_email, $to_name, $subject, $email_title, $email_content, $reply_to_email = null, $reply_to_name = null) {
    // Fetch settings from the database
    $settings = [];
    $result = $conn->query("SELECT setting_key, setting_value FROM settings");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }

    $mail = new PHPMailer(true);
    $site_name = $settings['site_name'] ?? 'Monogram Empire';

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $settings['smtp_host'] ?? '';
        $mail->SMTPAuth   = true;
        $mail->Username   = $settings['smtp_username'] ?? '';
        $mail->Password   = $settings['smtp_password'] ?? '';
        $mail->SMTPSecure = $settings['smtp_secure'] ?? PHPMailer::ENCRYPTION_STARTTLS;
       // $mail->Port       = isset($settings['smtp_port']) ? (int)$settings['smtp_port'] : 587;
        $mail->Port       =  587;

        // Recipients
        $mail->setFrom($settings['smtp_username'] ?? 'noreply@example.com', $site_name);
        $mail->addAddress($to_email, $to_name);

        if ($reply_to_email && $reply_to_name) {
             $mail->addReplyTo($reply_to_email, $reply_to_name);
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        
        // Use the new template function to generate the full HTML body
        $mail->Body = generate_email_html($site_name, $email_title, $email_content);

        $mail->send();
        return true;
    } catch (Exception $e) {
        // For debugging: error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

