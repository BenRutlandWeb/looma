<?php

namespace Looma\Acf\Commands;

use Looma\Console\CommandInterface;
use Looma\Console\Concerns\GeneratesFiles;
use Looma\Console\Concerns\HasOutput;
use Looma\Foundation\Application;
use Looma\Foundation\ServiceRepository;

final class MakeBlock implements CommandInterface
{
    use GeneratesFiles;
    use HasOutput;

    public string $name = 'make:block';

    public function __construct(public Application $app)
    {
        //
    }

    /**
     * Make a block directory with a block.json, style.css and template.php file.
     */
    public function __invoke(): void
    {
        $name = $this->ask('What is the name of block?');

        $slug = sanitize_title($name);

        $dir = $this->app->path('blocks/' . $slug);

        if ($this->exists($dir)) {
            $this->confirm('That block already exists. Do you want to overwrite it?');
        }

        $this->makeDirectory($dir);

        $stubs = [
            __DIR__ . '/stubs/block.json.stub'   => $dir . '/block.json',
            __DIR__ . '/stubs/style.css.stub'    => $dir . '/style.css',
            __DIR__ . '/stubs/template.php.stub' => $dir . '/template.php',
        ];

        foreach ($stubs as $stub => $path) {
            $contents = $this->getContents($stub);

            $contents = $this->replace($contents, [
                '{{ slug }}' => $slug,
                '{{ name }}' => $name,
            ]);

            $this->putContents($path, $contents);
        }

        $this->app->get(ServiceRepository::class)->set('blocks', $dir);

        $this->success("Block '$slug' created in $dir");
    }
}
