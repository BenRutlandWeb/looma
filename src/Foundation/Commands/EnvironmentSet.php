<?php

namespace Looma\Foundation\Commands;

use Looma\Console\CommandInterface;
use Looma\Console\Concerns\HasOutput;
use Looma\Foundation\Application;
use Looma\Foundation\Environment;

final class EnvironmentSet implements CommandInterface
{
    use HasOutput;

    public string $name = 'env:set';

    public function __construct(public readonly Application $app)
    {
        //
    }

    /**
     * Set the current environment.
     */
    public function __invoke(): void
    {
        $this->header('Looma', 'Set the current environment.');

        $env = $this->ask('What environment?', Environment::toArray(), $this->app->environment()->value);

        if ($this->callSilently("config set WP_ENVIRONMENT_TYPE {$env}")) {
            $this->success("Updated the environment to {$env}.")->terminate();
        }

        $this->error('Failed to update the environment.');
    }
}
