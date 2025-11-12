<?php

namespace Looma\Foundation\Events;

use Looma\Foundation\ServiceRepository;

final class CompileCache
{
    public function __construct(private ServiceRepository $manifest)
    {
        //
    }

    public function __invoke(): void
    {
        if (! $this->manifest->exists()) {
            $this->manifest->scan();
        }
    }
}
