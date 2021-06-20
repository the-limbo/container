<?php declare(strict_types=1);


namespace Limbo\Container\Reflection;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionException;
use ReflectionNamedType;
use ReflectionFunctionAbstract;
use Limbo\Container\Exception\NotFoundException;
use Limbo\Container\DefinitionContainerInterface;
use Limbo\Container\Exception\ContainerException;

/**
 * Class Reflector
 * @package Limbo\Container\Reflection
 */
class Reflector
{
    /**
     * @var Reflector
     */
    protected static Reflector $reflector;

    /**
     * @var DefinitionContainerInterface
     */
    protected DefinitionContainerInterface $container;

    /**
     * Reflector constructor.
     * @param DefinitionContainerInterface $container
     */
    public function __construct(DefinitionContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Set container for reflector and return Reflector instance
     * @param DefinitionContainerInterface $container
     * @return Reflector
     */
    public static function with(DefinitionContainerInterface $container): Reflector
    {
        return static::$reflector ?? new static($container);
    }

    /**
     * Reflect class and create instance
     * @param string $className
     * @param array $defaultArgs
     * @return object
     */
    public function resolveClass(string $className, array $defaultArgs = []): object
    {
        try {
            $reflection = new ReflectionClass($className);
            $constructor = $reflection->getConstructor();
            if (!$reflection->isInstantiable()) {
                throw new ContainerException(sprintf('Class "%s" is not instantiable.', $className));
            }

            if ($constructor === null) {
                return $reflection->newInstanceWithoutConstructor();
            }

            return $reflection->newInstanceArgs(
                $this->resolveArguments(
                    $this->reflectArguments($constructor, $defaultArgs)
                )
            );
        } catch (ReflectionException $e) {
            throw new NotFoundException(sprintf('Class "%s" not found or exists.', $className));
        }
    }

    /**
     * Reflect callable function and return result after call
     * @param callable $callable
     * @param array $defaultArgs
     * @return mixed
     */
    public function resolveCallable(callable $callable, array $defaultArgs = [])
    {
        try {
            $arguments = $this->resolveArguments(
                $this->reflectArguments(
                    new ReflectionFunction(Closure::fromCallable($callable)),
                    $defaultArgs
                )
            );

            return $callable(...$arguments);
        } catch (ReflectionException $e) {
            throw new NotFoundException(
                sprintf('Callable function "%s" not found.', $callable)
            );
        }
    }

    /**
     * Reflect method in class and return result after call
     * @param string $class
     * @param string $method
     * @param array $defaultArgs
     * @return mixed
     */
    public function resolveMethod(string $class, string $method, array $defaultArgs = [])
    {
        try {
            $reflection = new ReflectionMethod($class, $method);
            $arguments = $this->resolveArguments(
                $this->reflectArguments($reflection, $defaultArgs)
            );

            $class = $this->container->has($class)
                ? $this->container->make($class)
                : $this->resolveClass($class);

            return $reflection->invokeArgs($class, $arguments);
        } catch (ReflectionException $e) {
            throw new NotFoundException(
                sprintf('Method "%s" not found in class "%s"', $method, $class)
            );
        }
    }

    /**
     * Reflect arguments from method or function
     * @param ReflectionFunctionAbstract $method
     * @param array $defaultArgs
     * @return array
     */
    public function reflectArguments(ReflectionFunctionAbstract $method, array $defaultArgs = []): array
    {
        $parameters = $method->getParameters();
        $arguments = [];

        try {
            foreach ($parameters as $parameter) {
                $name = $parameter->getName();

                if (array_key_exists($name, $defaultArgs)) {
                    $arguments[] = $defaultArgs[$name];
                    continue;
                }

                $type = $parameter->getType();
                if ($type instanceof ReflectionNamedType) {
                    $typeHint = ltrim($type->getName(), '?');

                    if ($parameter->isDefaultValueAvailable()) {
                        $arguments[] = $parameter->getDefaultValue();
                        continue;
                    }

                    if (!$type->isBuiltin()) {
                        $arguments[] = $typeHint;
                        continue;
                    }

                    $arguments[] = $name;
                    continue;
                }

                if ($parameter->isDefaultValueAvailable()) {
                    $arguments[] = $parameter->getDefaultValue();
                    continue;
                }

                throw new NotFoundException(
                    sprintf('Parameter "%s" required, but not has default value.', $name)
                );
            }
        } catch (ReflectionException $e) {
            // ignore
        }

        return $arguments;
    }

    /**
     * Resolve arguments from container
     * @param array $arguments
     * @return array
     */
    public function resolveArguments(array $arguments): array
    {
        foreach ($arguments as &$argument) {
            if (!is_string($argument)) {
                continue;
            }

            if ($this->container->has($argument)) {
                $argument = $this->container->make($argument);
            }
        }

        return $arguments;
    }
}
