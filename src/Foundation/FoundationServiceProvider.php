<?php

namespace Looma\Foundation;

use Looma\Foundation\Application;
use Looma\Foundation\ServiceProviderInterface;

final class FoundationServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app): void
    {
        $app->singleton(ServiceRepository::class, fn() => new ServiceRepository($app, [
            // @todo move to the service providers
            'commands' => [
                'App\\Commands\\' => $app->path('Commands'),
            ],
            'blocks' => [
                $app->basePath . '/blocks',
            ],
        ]));
    }

    public function boot(Application $app): void
    {
        $app->commands([
            \Looma\Foundation\Commands\ClearCompiled::class,
        ]);
    }
}
