<?php

declare(strict_types=1);

namespace Limbo\Container\Definition;

use Limbo\Container\ContainerAwareTrait;
use Limbo\Container\Reflection\Reflector;

/**
 * Class Definition
 * @package Limbo\Container\Definition
 */
class Definition implements DefinitionInterface
{
    use ContainerAwareTrait;

    /**
     * Concrete instance in definition
     * @var mixed
     */
    protected $instance;

    /**
     * Resolved instance in definition
     * @var mixed
     */
    protected $resolved;

    /**
     * Shared definition (singleton)
     * @var bool
     */
    protected bool $shared = false;

    /**
     * Definition aliases
     * @var array
     */
    protected array $aliases = [];

    /**
     * Definition constructor.
     * @param string $id
     * @param mixed $instance
     */
    public function __construct(string $id, $instance = null)
    {
        $instance = $instance ?? $id;
        $this->instance = $instance;
        $this->aliases[] = $id;
    }

    /**
     * @inheritDoc
     */
    public function get()
    {
        return $this->instance;
    }

    /**
     * @inheritDoc
     */
    public function build(bool $new = false)
    {
        if ($new === true && $this->isShared() === false) {
            return $this->resolveNew();
        }

        return $this->resolve();
    }

    /**
     * @inheritDoc
     */
    public function addAlias(string $alias): DefinitionInterface
    {
        if (!$this->hasAlias($alias)) {
            $this->aliases[] = $alias;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasAlias(string $alias): bool
    {
        return in_array($alias, $this->aliases, true);
    }

    /**
     * @inheritDoc
     */
    public function setShared(bool $shared = false): DefinitionInterface
    {
        $this->shared = $shared;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isShared(): bool
    {
        return $this->shared;
    }

    /**
     * @return mixed
     */
    protected function resolve()
    {
        if ($this->resolved !== null && $this->isShared() === true) {
            return $this->resolved;
        }

        return $this->resolveNew();
    }

    /**
     * @return mixed
     */
    protected function resolveNew()
    {
        $instance = $this->instance;

        if (is_callable($instance)) {
            return Reflector::with($this->getContainer())->resolveCallable($instance);
        }

        if (is_string($instance) && class_exists($instance)) {
            $instance = Reflector::with($this->getContainer())->resolveClass($instance);
        }

        if (is_string($instance) && $this->getContainer()->has($instance)) {
            $instance = $this->getContainer()->make($instance, true);
        }

        $this->resolved = $instance;
        return $instance;
    }
}
