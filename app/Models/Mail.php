<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");

class Mail extends Model {

    protected $table = 'mailer_settings'; 
    protected $allowedColumns = [
        'host',
        'username',
        'password',
        'port',
        'from_name',
        'from_email',
        'replyto'
    ];

    /**
     * Send an email using SMTP settings stored in the database
     */
    public function sendEmail($to_email, $subject, $message) {
        
        // 1. Fetch settings from the database
        $settings = $this->getfirstrow();

        if (!$settings) {
            return "Mailer settings not found in database.";
        }

        // Require the PHPMailer source files from your app/Mailer folder
        require_once ROOTPATH . 'app/Mailer/Exception.php';
        require_once ROOTPATH . 'app/Mailer/PHPMailer.php';
        require_once ROOTPATH . 'app/Mailer/SMTP.php';

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $settings->host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $settings->username;
            $mail->Password   = $settings->password;
            $mail->SMTPSecure = ($settings->port == 465) ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $settings->port;

            // Recipients
            $mail->setFrom($settings->from_email, $settings->from_name);
            $mail->addAddress($to_email);
            
            if (!empty($settings->replyto)) {
                $mail->addReplyTo($settings->replyto, $settings->from_name);
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            return $mail->send();

        } catch (Exception $e) {
            return "Mail Error: {$mail->ErrorInfo}";
        }
    }

    public function getSmtp() {
        return $this->getfirstrow() ?: false;
    }
}