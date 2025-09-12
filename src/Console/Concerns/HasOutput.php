<?php

namespace Looma\Console\Concerns;

use WP_CLi;

trait HasOutput
{
    public function ask(string $question): string
    {
        WP_CLI::line($question);

        return trim(fgets(STDIN));
    }

    public function confirm(string $question): void
    {
        WP_CLI::confirm($question);
    }

    public function line(string $line): void
    {
        WP_CLI::line($line);
    }

    public function success(string $line): void
    {
        WP_CLI::success($line);
    }

    public function error(string $line, bool $exit = true): void
    {
        WP_CLI::error($line, $exit);
    }
}
