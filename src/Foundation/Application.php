<?php

namespace Looma\Foundation;

use Closure;
use Exception;
use Looma\Console\Console;
use Looma\Events\Dispatcher;
use Throwable;

final class Application
{
    private array $providers = [];

    private array $bindings = [];

    private array $instances = [];

    private array $extenders = [];

    public function __construct(public readonly string $basePath)
    {
        $this->registerCoreProviders();
    }

    public function path(string $path = ''): string
    {
        return wp_normalize_path(
            trim($this->basePath . '/app/' . trim($path, '/'), '/')
        );
    }

    public function cache(string $key, array $paths): void
    {
        $this->get(ServiceRepository::class)->cache($key, $paths);
    }

    public function environment(): string
    {
        return wp_get_environment_type();
    }

    public function inConsole(): string
    {
        return $this->get(Console::class)->active();
    }

    public function commands(array $commands): void
    {
        if ($this->inConsole()) {
            $console = $this->get(Console::class);

            foreach ($commands as $class) {
                $console->register(new $class($this));
            }
        }
    }

    public function events(array $events): void
    {
        $dispatcher = $this->get(Dispatcher::class);

        foreach ($events as $event => $listeners) {
            foreach ($listeners as $listener) {
                $dispatcher->listen($event, $listener);
            }
        }
    }

    public function registerCoreProviders(): void
    {
        $this->register(FoundationServiceProvider::class);
        $this->register(\Looma\Acf\AcfServiceProvider::class);
        $this->register(\Looma\Console\ConsoleServiceProvider::class);
        $this->register(\Looma\Events\EventServiceProvider::class);
    }

    public function register(string $provider): void
    {
        $provider = new $provider();

        $provider->register($this);

        $this->providers[] = $provider;
    }

    protected function bootProviders(): void
    {
        foreach ($this->providers as $provider) {
            $provider->boot($this);
        }
    }

    public function boot(): void
    {
        $dispatcher = $this->get(Dispatcher::class);

        $dispatcher->dispatch('looma:booting');

        $this->bootProviders();

        $dispatcher->dispatch('looma:booted');
    }

    public function singleton(string $id, Closure $binding): void
    {
        $this->bind($id, $binding, true);
    }

    public function bind(string $id, Closure $binding, bool $shared = false): void
    {
        $this->bindings[$id] = [$binding, $shared];
    }

    public function instance(string $id, mixed $instance): mixed
    {
        foreach ($this->extenders[$id] ?? [] as $extender) {
            $instance = $extender($instance, $this);
        }

        $this->instances[$id] = $instance;

        return $instance;
    }

    public function extend(string $id, Closure $callback): void
    {
        if (isset($this->instances[$id])) {
            $this->instances[$id] = $callback($this->instances[$id], $this);
        } else {
            $this->extenders[$id][] = $callback;
        }
    }

    /**
     * @template TAbstract
     * @param class-string<TAbstract>|string $id
     * @return ($id is class-string<TAbstract> ? TAbstract : mixed)
     */
    public function get(string $id): mixed
    {
        try {
            return $this->resolve($id);
        } catch (Throwable $e) {
            if ($this->has($id)) {
                throw new Exception($id, $e->getCode(), $e);
            }

            throw new Exception($id, $e->getCode(), $e);
        }
    }

    public function has(string $id): bool
    {
        return isset($this->bindings[$id]) || isset($this->instances[$id]);
    }

    /**
     * @template TAbstract
     * @param class-string<TAbstract>|string $id
     * @return ($id is class-string<TAbstract> ? TAbstract : mixed)
     */
    public function resolve(string $id, mixed ...$args): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        [$binding, $shared] = $this->bindings[$id];

        if ($binding instanceof Closure) {
            $instance = $binding($this, ...$args);

            foreach ($this->extenders[$id] ?? [] as $extender) {
                $instance = $extender($instance, $this);
            }

            if ($shared) {
                $this->instances[$id] = $instance;
            }

            return $instance;
        }

        throw new Exception($id);
    }
}
