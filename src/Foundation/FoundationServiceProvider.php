<?php

namespace Looma\Foundation;

final class FoundationServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app): void
    {
        $app->singleton(ServiceRepository::class, fn() => new ServiceRepository(
            $app,
            wp_get_upload_dir()['basedir'] . '/looma/cache.php',
        ));
    }

    public function boot(Application $app): void
    {
        $app->cache('service-providers', [
            $app->path('app/ServiceProviders'),
        ]);

        $app->commands([
            \Looma\Foundation\Commands\ClearCompiled::class,
            \Looma\Foundation\Commands\EnvironmentGet::class,
            \Looma\Foundation\Commands\EnvironmentSet::class,
            \Looma\Foundation\Commands\ListCommands::class,
            \Looma\Foundation\Commands\MakeServiceProvider::class,
        ]);

        $app->events([
            'looma:booted' => [
                \Looma\Foundation\Events\CompileCache::class,
            ],
        ]);
    }
}
