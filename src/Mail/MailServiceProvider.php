<?php

namespace Looma\Mail;

use Looma\Foundation\Application;
use Looma\Foundation\ServiceProviderInterface;

final class MailServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app): void
    {
        //
    }

    public function boot(Application $app): void
    {
        $app->events([
            'phpmailer_init' => [
                \Looma\Mail\Events\RegisterSmtpCredentials::class,
            ],
        ]);
    }
}
