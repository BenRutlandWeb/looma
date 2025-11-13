<?php

namespace Looma\Foundation;

use Closure;
use Exception;
use Looma\Console\Console;
use Looma\Events\Dispatcher;
use Looma\Foundation\Concerns\ContainerException;
use Looma\Foundation\Concerns\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionFunctionAbstract;
use Throwable;

final class Application implements ContainerInterface
{
    private array $providers = [];

    private array $bindings = [];

    private array $instances = [];

    private array $extenders = [];

    public function __construct(public readonly string $basePath)
    {
        $this->registerCoreProviders();

        $this->instance(static::class, $this);

        $this->instance(Environment::class, Environment::capture());
    }

    public function path(string $path = ''): string
    {
        return wp_normalize_path(
            trim($this->basePath . '/' . trim($path, '/'), '/')
        );
    }

    public function environment(): Environment
    {
        return $this->get(Environment::class);
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
                $console->register($this->make($class));
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

    public function cache(string $key, array $paths, bool $recursive = true): void
    {
        $this->get(ServiceRepository::class)->cache($key, $paths, $recursive);
    }

    public function registerCoreProviders(): void
    {
        $this->register(FoundationServiceProvider::class);
        $this->register(\Looma\Acf\AcfServiceProvider::class);
        $this->register(\Looma\Console\ConsoleServiceProvider::class);
        $this->register(\Looma\Events\EventServiceProvider::class);
    }

    /**
     * @template TServiceProvider
     * @param class-string<TServiceProvider> $provider
     * @return ServiceProviderInterface
     */
    public function register(string $provider): ServiceProviderInterface
    {
        $provider = $this->make($provider);

        $provider->register($this);

        return $this->providers[] = $provider;
    }

    public function bootProviders(): void
    {
        foreach ($this->providers as $provider) {
            $provider->boot($this);
        }
    }

    public function boot(): void
    {
        $dispatcher = $this->get(Dispatcher::class);

        $dispatcher->dispatch('looma:booting');

        $this->make(RegisterManifestServiceProviders::class)->register();

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

    /**
     * @template TInstance
     * @param TInstance $instance
     * @return TInstance
     */
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
                throw new ContainerException($e->getMessage(), $e->getCode(), $e);
            }

            throw new NotFoundException("Target class [$id] does not exist.", $e->getCode(), $e);
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

        [$binding, $shared] = $this->bindings[$id] ?? [null, null];

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

    /**
     * @template TAbstract
     * @param class-string<TAbstract> $class
     * @return TAbstract
     */
    public function make(string $abstract, array $parameters = []): object
    {
        $reflector = new ReflectionClass($abstract);

        if (! $reflector->isInstantiable()) {
            throw new ContainerException("Target [$abstract] is not instantiable.");
        }

        if ($constructor = $reflector->getConstructor()) {
            $arguments = $this->resolveFunctionParameters($constructor, $parameters);

            return $reflector->newInstanceArgs($arguments);
        }

        return new $abstract();
    }

    public function resolveFunctionParameters(ReflectionFunctionAbstract $function, array $parameters = []): array
    {
        $arguments = [];

        foreach ($function->getParameters() as $parameter) {
            $name = $parameter->getName();

            if (array_key_exists($name, $parameters)) {
                $arguments[] = $parameters[$name];

                continue;
            }

            $type = $parameter->getType();

            if ($type && ! $type->isBuiltin()) {
                $typeName = $type->getName();

                try {
                    $arguments[] = $this->get($typeName);

                    continue;
                } catch (Throwable) {
                    if (class_exists($typeName)) {
                        $arguments[] = $this->make($typeName);

                        continue;
                    }
                }
            }

            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();

                continue;
            }

            if ($parameter->allowsNull()) {
                $arguments[] = null;

                continue;
            }

            $declaring = $parameter->getDeclaringClass();

            $parent = $declaring ? $declaring->getName() : 'function';

            throw new ContainerException("Unable to resolve parameter {$name} in [$parent].");
        }

        return $arguments;
    }
}
