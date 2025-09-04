<?php

namespace Looma\Foundation\Commands;

use Looma\Console\CommandInterface;
use Looma\Console\Concerns\GeneratesFiles;
use Looma\Console\Concerns\HasOutput;
use Looma\Foundation\Application;
use Looma\Foundation\ServiceRepository;

final class ClearCompiled implements CommandInterface
{
    use GeneratesFiles;
    use HasOutput;

    public string $name = 'clear-compiled';

    public function __construct(public Application $app)
    {
        //
    }

    public function __invoke(): void
    {
        $this->app->get(ServiceRepository::class)->delete();

        $this->success('Manifest deleted');
    }
}
