<?php

namespace Looma\Console\Concerns;

trait GeneratesFiles
{
    public function resolveNamespace(string $rootNamespace, string $class): string
    {
        return $rootNamespace . (dirname($class) === '.' ? '' : '\\' . dirname($class));
    }

    public function resolveClass(string $class): string
    {
        return basename($class);
    }

    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function getContents(string $path): string
    {
        return file_get_contents($path);
    }

    public function putContents(string $path, string $contents): bool
    {
        return file_put_contents($path, $contents) !== false;
    }

    public function replace(string $contents, array $replacements): string
    {
        return str_replace(array_keys($replacements), array_values($replacements), $contents);
    }

    public function makeDirectory(string $path): bool
    {
        return wp_mkdir_p($path);
    }
}
