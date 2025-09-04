<?php

namespace Looma\Console;

use Looma\Foundation\Application;

interface CommandInterface
{
    public string $name { get; }

    public function __construct(Application $app);

    public function __invoke(): void;
}
