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

    public function __invoke(): void
    {
        $command = $this->ask('What is the command?');

        $class = str_replace(' ', '', ucwords(str_replace([':', '-', '_'], ' ', $command)));

        $path = $this->app->basePath . '/app/Commands/' . $class . '.php';

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
                '{{ namespace }}' => $namespace = $this->resolveNamespace('App\\Commands', $class),
                '{{ class }}'     => $resolvedClass = $this->resolveClass($class),
                '{{ command }}'   => $command,
            ]);

            $this->putContents($path, $contents);
        }

        $this->app->get(ServiceRepository::class)->set('commands', $namespace . '\\' . $resolvedClass);

        $this->success("Commands '$class' created.");
    }
}
