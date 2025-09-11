<?php

namespace Looma\Foundation;

final class ServiceRepository
{
    private readonly string $path;

    public function __construct(private Application $app, private array $scanDirs)
    {
        $this->path = $app->basePath . '/bootstrap/manifest.php';
    }

    public function all(): array
    {
        if (!file_exists($this->path)) {
            wp_mkdir_p(dirname($this->path));
            $this->write($this->scan());
        }

        return file_exists($this->path) ? require $this->path : [];
    }


    public function get(string $key): array
    {
        $return = [];

        foreach (($this->all()[$key] ?? []) as $class) {
            if (class_exists($class) || file_exists($class)) {
                $return[] = $class;
            }

            if (!class_exists($class) && !file_exists($class)) {
                $this->remove($key, $class);
            }
        }

        return $return;
    }

    public function set(string $key, string $class): void
    {
        $manifest = $this->all();

        if (!in_array($class, $manifest[$key] ?? [], true)) {
            $manifest[$key][] = $class;

            $this->write($manifest);
        }
    }

    public function remove(string $key, string $class): void
    {
        $manifest = $this->all() ?? [];

        $manifest[$key] = array_values(
            array_filter($manifest[$key], fn($c) => $c !== $class)
        );

        $this->write($manifest);
    }

    private function write(array $data): void
    {
        file_put_contents(
            $this->path,
            "<?php\nreturn " . var_export($data, true) . ";\n"
        );
    }

    protected function scan(): array
    {
        $classes = [];

        foreach (($this->scanDirs ?? []) as $key => $paths) {
            foreach ($paths as $namespace => $scanDir) {
                foreach (glob($scanDir . '/*.php') as $file) {
                    $className = $namespace . basename($file, '.php');

                    if (class_exists($className)) {
                        $classes[$key][] = $className;
                    } else {
                        $classes[$key][] = wp_normalize_path($file);
                    }
                }

                foreach (glob($scanDir . '/**') as $file) {
                    if (is_dir($file)) {
                        $classes[$key][] = wp_normalize_path($file);
                    }
                }
            }
        }

        ksort($classes);

        return $classes;
    }

    public function delete(): bool
    {
        return unlink($this->path);
    }
}
