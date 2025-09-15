<?php

namespace Looma\Foundation;

use Looma\Foundation\Application;
use Looma\Foundation\ServiceProviderInterface;

final class FoundationServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app): void
    {
        $app->singleton(ServiceRepository::class, fn() => new ServiceRepository(
            $app,
            $app->path('bootstrap/manifest.php'),
        ));
    }

    public function boot(Application $app): void
    {
        $app->cache('service-providers', [
            $app->path('app/ServiceProviders'),
        ]);

        $app->commands([
            \Looma\Foundation\Commands\ClearCompiled::class,
            \Looma\Foundation\Commands\GetEnvironment::class,
            \Looma\Foundation\Commands\ListCommands::class,
            \Looma\Foundation\Commands\MakeServiceProvider::class,
        ]);

        $app->events([
            'looma:booted' => [
                \Looma\Foundation\Events\CompileCache::class,
                \Looma\Foundation\Events\RegisterServiceProviders::class,
            ],
        ]);
    }
}
