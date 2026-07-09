<?php

namespace Looma\Mail\Events;

use Looma\Foundation\Env;
use PHPMailer\PHPMailer\PHPMailer;

final class RegisterSmtpCredentials
{
    public function __construct(public Env $env)
    {
        //
    }

    public function __invoke(PHPMailer $phpmailer): void
    {
        $env = $this->env;

        if ($env->get('MAIL_MAILER')) {
            $phpmailer->isSMTP();
            $phpmailer->SMTPSecure = $env->get('MAIL_ENCRYPTION', 'tls');
            $phpmailer->Host       = $env->get('MAIL_HOST');
            $phpmailer->Port       = $env->get('MAIL_PORT');
            $phpmailer->From       = $env->get('MAIL_FROM_ADDRESS');
            $phpmailer->FromName   = $env->get('MAIL_FROM_NAME');

            if ($env->get('MAIL_USERNAME')) {
                $phpmailer->SMTPAuth = true;
                $phpmailer->Username   = $env->get('MAIL_USERNAME');
                $phpmailer->Password   = $env->get('MAIL_PASSWORD');
            } else {
                $phpmailer->SMTPAuth = false;
            }
        }
    }
}
