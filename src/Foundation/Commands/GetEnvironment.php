<?php

namespace Looma\Foundation\Commands;

use Looma\Console\CommandInterface;
use Looma\Console\Concerns\GeneratesFiles;
use Looma\Console\Concerns\HasOutput;
use Looma\Foundation\Application;

final class GetEnvironment implements CommandInterface
{
    use GeneratesFiles;
    use HasOutput;

    public string $name = 'get:env';

    public function __construct(public readonly Application $app)
    {
        //
    }

    /**
     * Get the current environment.
     */
    public function __invoke(): void
    {
        $this->header('Looma', 'Get the current environment.');

        $this->info("The current environment is: {$this->app->environment()}");
    }
}
