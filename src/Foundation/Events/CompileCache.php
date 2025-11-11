<?php

namespace Looma\Foundation\Events;

use Looma\Foundation\Application;
use Looma\Foundation\ServiceRepository;

final class CompileCache
{
    public function __construct(private Application $app)
    {
        //
    }

    public function __invoke(): void
    {
        $repository = $this->app->get(ServiceRepository::class);

        if (! $repository->exists()) {
            $repository->scan();
        }
    }
}
