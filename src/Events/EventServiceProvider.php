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

        $events = (file_exists($app->basePath . '/app/Events/events.php'))
            ? include $app->basePath . '/app/Events/events.php'
            : [];

        $app->events($events);
    }
}
