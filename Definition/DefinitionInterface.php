<?php

declare(strict_types=1);

namespace Limbo\Container\Definition;

use Limbo\Container\ContainerAwareInterface;

/**
 * Interface DefinitionInterface
 * @package Limbo\Container\Definition
 */
interface DefinitionInterface extends ContainerAwareInterface
{
    /**
     * Get instance from definition
     * @return mixed
     */
    public function get();

    /**
     * Get new or resolved instance from definition
     * @param bool $new
     * @return mixed
     */
    public function build(bool $new = false);

    /**
     * Set shared type (singleton)
     * @param bool $shared
     * @return DefinitionInterface
     */
    public function setShared(bool $shared = false): DefinitionInterface;

    /**
     * Definition has shared type (singleton)
     * @return bool
     */
    public function isShared(): bool;

    /**
     * Add alias for definition
     * @param string $alias
     * @return DefinitionInterface
     */
    public function addAlias(string $alias): DefinitionInterface;

    /**
     * Definition has alias
     * @param string $alias
     * @return bool
     */
    public function hasAlias(string $alias): bool;
}
