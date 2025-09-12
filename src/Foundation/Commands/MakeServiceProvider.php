<?php

namespace Looma\Foundation\Commands;

use Looma\Console\CommandInterface;
use Looma\Console\Concerns\GeneratesFiles;
use Looma\Console\Concerns\HasOutput;
use Looma\Foundation\Application;

final class MakeServiceProvider implements CommandInterface
{
    use GeneratesFiles;
    use HasOutput;

    public string $name = 'make:service-provider';

    public function __construct(public Application $app)
    {
        //
    }

    /**
     * Make a service provider class.
     */
    public function __invoke(): void
    {
        $this->header('Make service provider', 'Make a service provider class.');

        do {
            $class = $this->ask('What is the name of class? E.g. AppServiceProvider');

            $valid = $this->validate($class);

            if (!$valid) {
                $this->error('Invalid. Not a classname.', false);
            }
        } while (!$valid);

        $path = $this->app->path('app/ServiceProviders/' . $class . '.php');

        if ($this->exists($path) && !$this->confirm('That service provider already exists. Do you want to overwrite it?', false)) {
            $this->error('Service provider creation cancelled.');
        }

        $this->makeDirectory(dirname($path));

        $stubs = [
            __DIR__ . '/stubs/service-provider.php.stub' => $path,
        ];

        foreach ($stubs as $stub => $path) {
            $contents = $this->getContents($stub);

            $contents = $this->replace($contents, [
                '{{ namespace }}' => $this->resolveNamespace('App\\ServiceProviders', $class),
                '{{ class }}'     => $this->resolveClass($class),
            ]);

            $this->putContents($path, $contents);
        }

        $this->success("Service provider '$class' created.");
    }

    public function validate(string $class): bool
    {
        return (bool) preg_match('/^(?:[A-Z][A-Za-z0-9_]*\\\\)*[A-Z][A-Za-z0-9_]*$/', $class);
    }
}
