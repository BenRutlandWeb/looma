<?php

namespace Looma\Console;

use Looma\Foundation\Application;
use Looma\Foundation\ServiceProviderInterface;
use Looma\Foundation\ServiceRepository;

final class ConsoleServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app): void
    {
        $app->singleton(Console::class, fn() => new Console());
    }

    public function boot(Application $app): void
    {
        $app->cache('commands', [
            $app->path('app/Commands'),
        ]);

        $commands = $app->get(ServiceRepository::class)->get('commands');

        $app->commands(array_merge($commands, [
            \Looma\Console\Commands\MakeCommand::class,
        ]));
    }
}
