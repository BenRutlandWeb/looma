<?php

namespace Looma\Events;

use InvalidArgumentException;
use Looma\Foundation\Application;
use ReflectionFunction;
use ReflectionMethod;

final class Dispatcher
{
    public function __construct(private Application $app)
    {
        //
    }

    public function listen(string $event, string $callable): void
    {
        [$callable, $priority, $parameterCount] = $this->resolveCallable($callable);

        add_filter($event, $callable, $priority, $parameterCount);
    }

    public function dispatch(string $event, mixed ...$args): mixed
    {
        return apply_filters($event, ...$args ?: [null]);
    }

    private function resolveCallable(string $callable): array
    {
        if (function_exists($callable)) {
            $ref = new ReflectionFunction($callable);
        } elseif (class_exists($callable)) {
            $callable = new $callable($this->app);
            $ref = new ReflectionMethod($callable, '__invoke');
        } else {
            throw new InvalidArgumentException("Not a function or class: $callable");
        }

        $attribute = $ref->getAttributes(Priority::class)[0] ?? null;

        return [
            $callable,
            $attribute ? $attribute->newInstance()->priority : 10,
            $ref->getNumberOfParameters(),
        ];
    }
}
