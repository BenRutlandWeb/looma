<?php

namespace Looma\Acf;

use Looma\Foundation\Application;
use Looma\Foundation\ServiceProviderInterface;

final class AcfServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app): void {}

    public function boot(Application $app): void
    {
        $app->cache('blocks', [
            $app->path('blocks'),
        ], false);

        $app->commands([
            \Looma\Acf\Commands\MakeBlock::class,
        ]);

        $app->events([
            'acf/blocks/no_fields_assigned_message' => [
                '__return_false',
            ],
            'init' => [
                \Looma\Acf\Events\RegisterBlocks::class,
            ]
        ]);
    }
}
