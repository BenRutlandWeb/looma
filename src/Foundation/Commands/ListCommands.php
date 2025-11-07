<?php

namespace Looma\Foundation\Commands;

use Looma\Console\CommandInterface;
use Looma\Console\Concerns\HasOutput;
use Looma\Foundation\Application;
use WP_CLI;

final class ListCommands implements CommandInterface
{
    use HasOutput;

    public string $name = 'list';

    public function __construct(public readonly Application $app)
    {
        //
    }

    /**
     * List Looma commands.
     */
    public function __invoke(): void
    {
        $this->header('Looma', 'List Looma commands.');

        [$command] = WP_CLI::get_runner()->find_command_to_run(['looma']);

        $commands = [];

        foreach ($command->get_subcommands() as $command) {
            $commands[$command->get_name()] = $command->get_shortdesc();
        }

        $this->headedList('Commands', $commands);
    }
}
