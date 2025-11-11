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

        $this->loadEvents($app);
    }

    public function loadEvents(Application $app): void
    {
        $path = $app->path('app/Events/events.php');

        if (file_exists($path)) {
            $app->events(include $path);
        } else {
            file_put_contents($path, file_get_contents(__DIR__ . '/stubs/events.php.stub'));
        }
    }
}
