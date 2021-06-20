<?php declare(strict_types=1);


namespace Limbo\Container;

/**
 * Trait ContainerAwareTrait
 * @package Limbo\Container
 */
trait ContainerAwareTrait
{
    /**
     * DI container
     * @var DefinitionContainerInterface
     */
    protected DefinitionContainerInterface $container;

    /**
     * @inheritDoc
     */
    public function setContainer(DefinitionContainerInterface $container): self
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContainer(): DefinitionContainerInterface
    {
        return $this->container;
    }
}
