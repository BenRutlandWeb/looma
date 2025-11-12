<?php

namespace Looma\Foundation\Commands;

use Looma\Console\CommandInterface;
use Looma\Console\Concerns\HasOutput;
use Looma\Foundation\Environment;

final class EnvironmentGet implements CommandInterface
{
    use HasOutput;

    public string $name = 'env:get';

    public function __construct(public readonly Environment $env)
    {
        //
    }

    /**
     * Get the current environment.
     */
    public function __invoke(): void
    {
        $this->header('Looma', 'Get the current environment.');

        $this->info("The current environment is: {$this->env->value}.");
    }
}
