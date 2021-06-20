<?php

declare(strict_types=1);

namespace Limbo\Container;

use InvalidArgumentException;
use Limbo\Container\Reflection\Reflector;
use Limbo\Container\Definition\Definition;
use Limbo\Container\Exception\NotFoundException;
use Limbo\Container\Definition\DefinitionInterface;

/**
 * Class Container
 * @package Limbo\Container
 */
class Container implements DefinitionContainerInterface
{
    /**
     * Array of container bindings.
     * @var DefinitionInterface[]
     */
    protected array $bindings = [];

    /**
     * Array of cached classes that be reflected.
     * @var array
     */
    protected array $cache = [];

    /**
     * Container constructor.
     * @param DefinitionInterface[] $definitions
     */
    public function __construct(array $definitions = [])
    {
        $this->bindings = array_filter($definitions, function($definition) {
            return ($definition instanceof DefinitionInterface);
        });

        array_walk($this->bindings, fn(DefinitionInterface $binding) => $binding->setContainer($this));
    }

    /**
     * @inheritDoc
     */
    public function bind(string $id, $concrete = null): DefinitionInterface
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Definition name cannot be empty.');
        }

        if (!$concrete instanceof DefinitionInterface) {
            return $this->bindings[] = (new Definition($id, $concrete))->setContainer($this);
        }

        return $this->bindings[] = $concrete->addAlias($id)->setContainer($this);
    }

    /**
     * @inheritDoc
     */
    public function singleton(string $id, $concrete = null): DefinitionInterface
    {
        return $this->bind($id, $concrete)->setShared(true);
    }

    /**
     * @inheritDoc
     */
    public function make(string $id, bool $new = false)
    {
        foreach ($this->bindings as $definition) {
            if ($definition->hasAlias($id)) {
                return $definition->build($new);
            }
        }

        if (class_exists($id)) {
            if (array_key_exists($id, $this->cache)) {
                return $this->cache[$id];
            }

            return $this->cache[$id] = Reflector::with($this)->resolveClass($id);
        }

        throw new NotFoundException(
            sprintf('Definition with alias "%s" not found in container or is not an existing class.', $id)
        );
    }

    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        return $this->make($id);
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        foreach ($this->bindings as $definition) {
            if ($definition->hasAlias($id)) {
                return true;
            }
        }

        if (class_exists($id)) {
            return true;
        }

        return false;
    }
}
