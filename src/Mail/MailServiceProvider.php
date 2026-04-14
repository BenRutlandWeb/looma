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
                // @todo credentials need to be passed via the container rather
                // than the $_ENV global.
                // \Looma\Mail\Events\RegisterSmtpCredentials::class,
            ],
        ]);
    }
}
