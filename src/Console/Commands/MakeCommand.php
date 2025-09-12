<?php

namespace Looma\Console\Commands;

use Looma\Console\CommandInterface;
use Looma\Console\Concerns\GeneratesFiles;
use Looma\Console\Concerns\HasOutput;
use Looma\Foundation\Application;
use Looma\Foundation\ServiceRepository;

final class MakeCommand implements CommandInterface
{
    use GeneratesFiles;
    use HasOutput;

    public string $name = 'make:command';

    public function __construct(public Application $app)
    {
        //
    }

    /**
     * Make a command class.
     */
    public function __invoke(): void
    {
        do {
            $command = $this->ask('What is the command?');

            $valid = $this->validate($command);

            if (! $valid) {
                $this->error('Invalid format. Use lowercase alphanumeric characters, colons and hyphens only.', false);
            }
        } while (!$valid);

        $class = str_replace(' ', '', ucwords(str_replace([':', '-'], ' ', $command)));

        $path = $this->app->path('app/Commands/' . $class . '.php');

        if ($this->exists($path)) {
            $this->confirm('That command already exists. Do you want to overwrite it?');
        }

        $this->makeDirectory(dirname($path));

        $stubs = [
            __DIR__ . '/stubs/command.php.stub' => $path,
        ];

        foreach ($stubs as $stub => $path) {
            $contents = $this->getContents($stub);

            $contents = $this->replace($contents, [
                '{{ namespace }}' => $this->resolveNamespace('App\\Commands', $class),
                '{{ class }}'     => $this->resolveClass($class),
                '{{ command }}'   => basename($command),
            ]);

            $this->putContents($path, $contents);
        }

        $this->app->get(ServiceRepository::class)->set('commands', $path);

        $this->success("Commands '$class' created.");
    }

    public function validate(string $command): bool
    {
        return (bool) preg_match('/^[a-z0-9:-]+$/', $command);
    }
}
