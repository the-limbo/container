<?php declare(strict_types=1);


namespace Limbo\Container;

use Psr\Container\ContainerInterface;
use Limbo\Container\Definition\DefinitionInterface;

/**
 * Interface DefinitionContainerInterface
 * @package Limbo\Container
 */
interface DefinitionContainerInterface extends ContainerInterface
{
    /**
     * Bind definition in container
     * @param string $id
     * @param null $concrete
     * @return DefinitionInterface
     */
    public function bind(string $id, $concrete = null): DefinitionInterface;

    /**
     * Bind singleton definition in container
     * @param string $id
     * @param null $concrete
     * @return DefinitionInterface
     */
    public function singleton(string $id, $concrete = null): DefinitionInterface;

    /**
     * Get instance from container (alias for method get() from ContainerInterface)
     * @param string $id
     * @param bool $new
     * @return mixed
     */
    public function make(string $id, bool $new = false);
}
