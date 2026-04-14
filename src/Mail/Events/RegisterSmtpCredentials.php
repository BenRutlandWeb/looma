<?php

namespace Looma\Mail\Events;

use PHPMailer\PHPMailer\PHPMailer;

final class RegisterSmtpCredentials
{
    public function __invoke(PHPMailer $phpmailer): void
    {
        // @todo credentials need to be passed via the container rather
        // than the $_ENV global.
        if ($_ENV['MAIL_MAILER'] ?? null) {
            $phpmailer->isSMTP();
            $phpmailer->SMTPAuth   = true;
            $phpmailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
            $phpmailer->Host       = $_ENV['MAIL_HOST'] ?? null;
            $phpmailer->Port       = $_ENV['MAIL_PORT'] ?? null;
            $phpmailer->Username   = $_ENV['MAIL_USERNAME'] ?? null;
            $phpmailer->Password   = $_ENV['MAIL_PASSWORD'] ?? null;
            $phpmailer->From       = $_ENV['MAIL_FROM_ADDRESS'] ?? '';
            $phpmailer->FromName   = $_ENV['MAIL_FROM_NAME'] ?? '';
            $phpmailer->IsHTML(true);
        }
    }
}
