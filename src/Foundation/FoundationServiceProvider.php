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

        $app->singleton(Env::class, fn() => new Env($_ENV));
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
            // @todo key:generate could and maybe should use aaemnnosttv/wp-cli-dotenv-command
            // \Looma\Foundation\Commands\KeyGenerate::class,
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
