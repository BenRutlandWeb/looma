<?php

namespace Looma\Events\Commands;

use Looma\Console\CommandInterface;
use Looma\Console\Concerns\GeneratesFiles;
use Looma\Console\Concerns\HasOutput;
use Looma\Foundation\Application;

final class MakeListener implements CommandInterface
{
    use GeneratesFiles;
    use HasOutput;

    public string $name = 'make:listener';

    public function __construct(public Application $app)
    {
        //
    }

    /**
     * Make a listener class.
     */
    public function __invoke(): void
    {
        $class = $this->ask('What is the name of class?');

        $path = $this->app->path('app/Events/Listeners/' . $class . '.php');

        if ($this->exists($path)) {
            $this->confirm('That listener already exists. Do you want to overwrite it?');
        }

        $this->makeDirectory(dirname($path));

        $stubs = [
            __DIR__ . '/stubs/listener.php.stub' => $path,
        ];

        foreach ($stubs as $stub => $path) {
            $contents = $this->getContents($stub);

            $contents = $this->replace($contents, [
                '{{ namespace }}' => $this->resolveNamespace('App\\Events\\Listeners', $class),
                '{{ class }}'     => $this->resolveClass($class),
            ]);

            $this->putContents($path, $contents);
        }

        $this->success("Listener '$class' created.");
    }
}
