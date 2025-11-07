<?php

namespace Looma\Foundation\Commands;

use Looma\Console\CommandInterface;
use Looma\Console\Concerns\HasOutput;
use Looma\Foundation\Application;
use Looma\Foundation\ServiceRepository;

final class ClearCompiled implements CommandInterface
{
    use HasOutput;

    public string $name = 'clear-compiled';

    public function __construct(public readonly Application $app)
    {
        //
    }

    /**
     * Clear the compiled bootstrap manifest.
     */
    public function __invoke(): void
    {
        $this->header('Looma', 'Clear the compiled bootstrap manifest.');

        $this->app->get(ServiceRepository::class)->delete();

        $this->success('Manifest deleted');
    }
}
