<?php

namespace Looma\Foundation;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class ServiceRepository
{
    private readonly string $path;

    private array $data = [];

    public function __construct(private Application $app, private array $scanDirs = [])
    {
        $this->path = $app->basePath . '/bootstrap/manifest.php';

        if ($this->exists()) {
            $this->data = require $this->path;
        } else {
            wp_mkdir_p(dirname($this->path));
        }
    }

    public function exists(): bool
    {
        return file_exists($this->path);
    }

    public function cache(string $key, array $paths, bool $recursive = true): void
    {
        $paths = array_map(fn($d) => [wp_normalize_path($d), $recursive], $paths);

        $this->scanDirs[$key] = array_merge_recursive($this->scanDirs[$key] ?? [], $paths);
    }

    public function get(string $key): array
    {
        $return = [];

        foreach (($this->data[$key] ?? []) as $path) {
            $class = str_replace([$this->app->path('app'), '.php', '/'], ['App', '', '\\'], $path);

            if (class_exists($class)) {
                $return[] = $class;
                continue;
            }

            if (file_exists($path)) {
                $return[] = $path;
                continue;
            }

            $this->remove($key, $path);
        }

        return $return;
    }

    public function set(string $key, string $path): void
    {
        $manifest = $this->data;

        if (!in_array($path, $manifest[$key] ?? [])) {
            $manifest[$key][] = wp_normalize_path($path);

            sort($manifest[$key]);

            $this->write($manifest);
        }
    }

    public function remove(string $key, string $path): void
    {
        $manifest = $this->data;

        $manifest[$key] = array_values(
            array_filter($manifest[$key], fn($p) => $p !== $path)
        );

        $this->write($manifest);
    }

    private function write(array $data): void
    {
        $this->data = $data;

        file_put_contents(
            $this->path,
            "<?php\nreturn " . var_export($data, true) . ";\n"
        );
    }

    public function scan(): array
    {
        $files = [];

        foreach ($this->scanDirs as $key => $paths) {
            foreach ($paths as [$path, $recursive]) {
                if (!is_dir($path)) {
                    continue;
                }

                $rii = $recursive
                    ? new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
                    )
                    : new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);

                foreach ($rii as $file) {
                    $files[$key][] = wp_normalize_path($file->getPathname());
                    continue;
                }
            }
        }

        ksort($files);

        $this->write($files);

        return $files;
    }

    public function delete(): bool
    {
        return unlink($this->path);
    }
}
