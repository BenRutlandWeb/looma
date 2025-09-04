<?php

namespace Looma\Console;

class Console
{
    public function active(): bool
    {
        return class_exists(WP_CLI::class);
    }

    public function register(CommandInterface $command): void
    {
        WP_CLI::add_command('looma ' . $command->name, $command);
    }
}
