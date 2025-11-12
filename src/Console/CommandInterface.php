<?php

namespace Looma\Console;

interface CommandInterface
{
    public string $name { get; }

    public function __invoke(array $arguments = [], array $options = []): void;
}
