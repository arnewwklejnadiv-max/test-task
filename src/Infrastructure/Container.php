<?php

declare(strict_types=1);

namespace App\Infrastructure;

use ReflectionClass;
use ReflectionFunction;
use ReflectionNamedType;
use Throwable;

final class Container
{
    /** @var array<string, object> */
    private array $instances = [];

    /** @var array<string, true> */
    private array $resolving = [];

    /** @param array<string, callable(self): object|class-string> $definitions */
    public function __construct(private array $definitions) {}

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     */
    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (isset($this->resolving[$id])) {
            throw new ContainerException('Циклическая зависимость: ' . $id);
        }

        $this->resolving[$id] = true;
        try {
            $instance = isset($this->definitions[$id])
                ? $this->resolve($this->definitions[$id])
                : $this->autowire($id);
        } catch (ContainerException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new ContainerException($exception->getMessage(), 0, $exception);
        } finally {
            unset($this->resolving[$id]);
        }

        return $this->instances[$id] = $instance;
    }

    /** @param callable(self): object|class-string $definition */
    private function resolve(callable|string $definition): object
    {
        if (is_string($definition)) {
            return $this->get($definition);
        }

        $function = new ReflectionFunction($definition);
        $instance = $function->getNumberOfParameters() === 0 ? $definition() : $definition($this);
        if (!is_object($instance)) {
            throw new ContainerException('Фабрика контейнера должна вернуть объект');
        }

        return $instance;
    }

    private function autowire(string $class): object
    {
        if (!class_exists($class)) {
            throw new ContainerException('Сервис не зарегистрирован: ' . $class);
        }

        $reflection = new ReflectionClass($class);
        if (!$reflection->isInstantiable()) {
            throw new ContainerException('Сервис не зарегистрирован: ' . $class);
        }

        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $arguments = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $arguments[] = $parameter->getDefaultValue();
                    continue;
                }

                throw new ContainerException('Нельзя автоматически создать ' . $class . ': аргумент $' . $parameter->getName() . ' нужно зарегистрировать в контейнере');
            }

            $arguments[] = $this->get($type->getName());
        }

        return $reflection->newInstanceArgs($arguments);
    }
}
