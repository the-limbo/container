<?php declare(strict_types=1);


namespace Limbo\Container;

/**
 * Interface ContainerAwareInterface
 * @package Limbo\Container
 */
interface ContainerAwareInterface
{
    /**
     * Set DI container
     * @param DefinitionContainerInterface $container
     * @return static
     */
    public function setContainer(DefinitionContainerInterface $container): self;

    /**
     * Get DI container
     * @return DefinitionContainerInterface
     */
    public function getContainer(): DefinitionContainerInterface;
}
