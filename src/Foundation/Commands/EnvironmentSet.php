<?php

namespace Looma\Foundation\Commands;

use Looma\Console\CommandInterface;
use Looma\Console\Concerns\HasOutput;
use Looma\Foundation\Application;

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

        $env = $this->ask('What environment?', ['local', 'development', 'staging', 'production'], $this->app->environment());

        if ($this->callSilently(sprintf('config set WP_ENVIRONMENT_TYPE %s', $env))) {
            $this->success(sprintf('Updated the environment to %s', $env))
                ->terminate();
        }

        $this->error('Failed to update the environment');
    }
}
