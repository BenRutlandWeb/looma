<?php

namespace Looma\Console\Concerns;

use Closure;
use WP_CLI;

use function WP_CLI\Utils\make_progress_bar as wpcli_make_progress_bar;

trait HasOutput
{
    public function success(string $message, bool $exit = false): static
    {
        if ($exit) {
            $this->terminate("<success>{$message}</success>");
        }

        return $this->line("<success>{$message}</success>")->newLine();
    }

    public function warning(string $message): static
    {
        return $this->line("<warning>{$message}</warning>");
    }

    public function error(string $message, bool $exit = true): void
    {
        if ($exit) {
            $this->terminate("<error>{$message}</error>");
        }

        $this->line("<error>{$message}</error>")->newLine();
    }

    public function info(string $message): static
    {
        return $this->line("<info>{$message}</info>");
    }

    public function comment(string $message): static
    {
        return $this->line("<comment>{$message}</comment>");
    }

    public function line(string $message): static
    {
        WP_CLI::log($this->colorize($message));

        return $this;
    }

    public function newLine(int $lines = 1): static
    {
        while ($lines > 0) {
            $this->line('');
            $lines--;
        }

        return $this;
    }

    public function colorize(string $message): string
    {
        $map = [
            // colors
            'info' => "\033[96m",
            'error' => "\033[91m",
            'warning' => "\033[33m",
            'success' => "\033[92m",
            'comment' => "\033[38;5;232m",

            'red' => "\033[38;5;196m",
            'bg-red' => "\033[48;5;196m",
            'pink' => "\033[38;5;200m",
            'bg-pink' => "\033[48;5;200m",
            'purple' => "\033[38;5;93m",
            'bg-purple' => "\033[48;5;93m",
            'indigo' => "\033[38;5;62m",
            'bg-indigo' => "\033[48;5;62m",
            'blue' => "\033[38;5;39m",
            'bg-blue' => "\033[48;5;39m",
            'light-blue' => "\033[38;5;117m",
            'bg-light-blue' => "\033[48;5;117m",
            'cyan' => "\033[38;5;51m",
            'bg-cyan' => "\033[48;5;51m",
            'teal' => "\033[38;5;37m",
            'bg-teal' => "\033[48;5;37m",
            'green' => "\033[38;5;40m",
            'bg-green' => "\033[48;5;40m",
            'light-green' => "\033[38;5;113m",
            'bg-light-green' => "\033[48;5;113m",
            'lime' => "\033[38;5;154m",
            'bg-lime' => "\033[48;5;154m",
            'yellow' => "\033[38;5;226m",
            'bg-yellow' => "\033[48;5;226m",
            'amber' => "\033[38;5;214m",
            'bg-amber' => "\033[48;5;214m",
            'orange' => "\033[38;5;208m",
            'bg-orange' => "\033[48;5;208m",
            'deep-orange' => "\033[38;5;202m",
            'bg-deep-orange' => "\033[48;5;202m",
            'brown' => "\033[38;5;94m",
            'bg-brown' => "\033[48;5;94m",
            'white' => "\033[38;5;15m",
            'bg-white' => "\033[48;5;15m",
            'light-grey' => "\033[38;5;249m",
            'bg-light-grey' => "\033[48;5;249m",
            'grey' => "\033[38;5;232m",
            'bg-grey' => "\033[48;5;232m",
            'dark-grey' => "\033[38;5;232m\033[2m",
            'bg-dark-grey' => "\033[48;5;232m\033[2m",

            // format
            'i' => "\033[3m",
            'b' => "\033[1m",
            'u' => "\033[4m",
        ];

        $message = preg_replace_callback('#<(.+?)>#i', function ($match) use ($map) {
            return $map[strtolower($match[1])] ?? $match[0];
        }, $message);

        return preg_replace('#</(.+?)>#i', "\033[0m", $message);
    }

    public function progressBar(array $data, Closure $handler, string $message = ''): static
    {
        $bar = wpcli_make_progress_bar(
            $this->colorize("<light-blue>{$message}</light-blue>"),
            count($data)
        );

        foreach ($data as $value) {
            $handler($value);
            $bar->tick();
        }

        $bar->finish();

        return $this->newLine();
    }

    public function confirm(string $question, bool $default = true, string $yes = 'Yes', string $no = 'No'): bool
    {
        return $this->ask($question, [$yes, $no], $default ? $yes : $no) === $yes;
    }

    public function ask(string $question, array $options = [], mixed $default = null, bool $multiple = false)
    {
        if (! empty($options)) {
            return $this->select($question, $options, $default, $multiple);
        }

        while (true) {
            $this->line("<light-blue>{$question}</light-blue>");

            if ($answer = trim(fgets(STDIN))) {
                $this->newLine();

                return $answer;
            }

            $this->replacePreviousLine("<warning>Input is required.</warning>")->newLine();
        }
    }

    public function select(string $question, array $options, mixed $default = null, bool $multiple = false)
    {
        if ($multiple) {
            return $this->selectMultiple($question, $options, $default);
        }

        while (true) {
            $this->line("<light-blue>{$question}</light-blue>")
                ->optionList($options, $default);

            $input = trim(fgets(STDIN));

            if ($input === '') {
                if ($default) {
                    $this->replacePreviousLine($default)->newLine();

                    return $default;
                }
                $this->replacePreviousLine("<warning>Input is required.</warning>")->newLine();

                continue;
            }

            if (isset($options[$input])) {
                $this->replacePreviousLine($options[$input])->newLine();

                return $options[$input];
            }

            $this->replacePreviousLine("<warning>Input {$input} is invalid.</warning>")->newLine();

            continue;
        }
    }

    public function selectMultiple(string $question, array $options, mixed $defaults = null)
    {
        array_unshift($options, 'All options');

        while (true) {
            $this->line("<light-blue>{$question}</light-blue>")
                ->comment('Select multiple items using commas.')
                ->optionList($options, $defaults);

            $input = trim(fgets(STDIN));

            if ($input === '0') {
                return $options;
            }

            $defaults = $defaults ? (is_array($defaults) ? $defaults : [$defaults]) : null;

            if ($input === '') {
                if ($defaults) {
                    $this->replacePreviousLine(implode(', ', $defaults))->newLine();

                    return $defaults;
                }

                $this->replacePreviousLine("<warning>Input is required.</warning>")->newLine();

                continue;
            }

            $selected = explode(',', $input);

            $validSelections = [];
            $invalidSelections = [];

            foreach ($selected as $s) {
                if (isset($options[trim($s)])) {
                    $validSelections[] = $options[$s];
                } else {
                    $invalidSelections[] = $s;
                }
            }

            if (empty($invalidSelections)) {
                $this->replacePreviousLine(implode(', ', $validSelections))->newLine();

                return $validSelections;
            }

            $err = implode(', ', $invalidSelections);
            $this->replacePreviousLine("<warning>Input {$err} is invalid.</warning>")->newLine();

            continue;
        }
    }

    public function terminate(?string $message = null): void
    {
        if ($message) {
            $this->line($message);
        }

        die;
    }

    public function optionList(array $options, mixed $default = null): static
    {
        $defaults = is_array($default) ? $default : [$default];

        $max = strlen((string) count($options));

        foreach ($options as $i => $label) {
            $num = str_repeat(' ', $max - strlen((string) $i));
            $this->line(sprintf('- [%s] %s', in_array($label, $defaults) ? "<light-blue>{$i}</light-blue>" : $i, $num . $label));
        }

        return $this;
    }

    public function list(array $options, bool $full = false, string $sep = '.', ?string $template = null, ?int $maxWidth = null): static
    {
        $max = $full ? ($maxWidth ?? $this->getTerminalWidth() - 4) : ($maxWidth ?? max(array_map('strlen', array_keys($options))));

        foreach ($options as $key => $label) {
            $seps = str_repeat($sep, $max - strlen($key . ($full ? $label : '')));

            $args = compact('key', 'seps', 'label');

            $this->line(preg_replace_callback('/%([a-zA-Z_][a-zA-Z0-9_]*)/', function ($m) use ($args) {
                return array_key_exists($m[1], $args) ? $args[$m[1]] : $m[0];
            }, $template ?? '%key %seps %label'));
        }

        return $this->newLine();
    }

    public function header(string $heading, string $subheading = ''): static
    {
        $this->newLine()->line('<light-blue>' . strtoupper($heading) . '</light-blue>');

        if ($subheading) {
            $this->line("<light-grey>{$subheading}</light-grey>");
        }

        return $this->divider()->newLine();
    }

    public function divider(?int $length = null): static
    {
        return $this->comment(str_repeat('-', $length ?? $this->getTerminalWidth()));
    }

    public function headedList(string $heading, array $options): static
    {
        return $this->line('<light-blue>' . strtoupper($heading) . '</light-blue>')
            ->list($options, false, '.', '%key <dark-grey>%seps</dark-grey> <grey>%label</grey>', 24);
    }

    public function replacePreviousLine(string $string, int $previous = 1): static
    {
        return $this->line("\033[{$previous}F\033[0K{$string}");
    }

    public function getTerminalWidth(): int
    {
        $width = (int) @exec('tput cols 2>/dev/null 2>&1');

        if ($width > 0) {
            return $width;
        }

        $output = [];

        @exec('mode con >nul 2>&1', $output);
        if ($output) {
            foreach ($output as $line) {
                if (preg_match('/Columns:\s+(\d+)/i', $line, $m)) {
                    return (int) $m[1];
                }
            }
        }

        return 80;
    }

    public function call(string $command, array $arguments = [], bool $silent = false, bool $launch = true): bool
    {
        $return = WP_CLI::runcommand($command, [
            'return' => true,
            'launch' => $launch,
            'exit_error' => false,
            'command_args' => $arguments,
        ]);

        if (! $silent) {
            echo $return;
            $this->newLine();
        }

        return $return ? true : false;
    }

    public function callSilently(string $command, array $arguments = []): bool
    {
        return $this->call($command, $arguments, true);
    }
}
