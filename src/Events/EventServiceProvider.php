<?php

namespace Looma\Events;

use Looma\Foundation\Application;
use Looma\Foundation\ServiceProviderInterface;

final class EventServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app): void
    {
        $app->singleton(Dispatcher::class, fn() => new Dispatcher($app));
    }

    public function boot(Application $app): void
    {
        $app->commands([
            \Looma\Events\Commands\MakeListener::class,
        ]);

        if (file_exists($app->path('app/Events/events.php'))) {
            $app->events(include $app->path('app/Events/events.php'));
        }
    }
}
