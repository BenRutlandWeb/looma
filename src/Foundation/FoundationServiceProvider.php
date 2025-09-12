<?php

namespace Looma\Foundation;

use Looma\Foundation\Application;
use Looma\Foundation\Events\CompileCache;
use Looma\Foundation\ServiceProviderInterface;

final class FoundationServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app): void
    {
        $app->singleton(ServiceRepository::class, fn() => new ServiceRepository($app));
    }

    public function boot(Application $app): void
    {
        $app->commands([
            \Looma\Foundation\Commands\ClearCompiled::class,
        ]);

        $app->events([
            'looma:booted' => [
                CompileCache::class,
            ],
        ]);
    }
}
