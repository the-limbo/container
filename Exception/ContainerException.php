<?php declare(strict_types=1);


namespace Limbo\Container\Exception;

use RuntimeException;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends RuntimeException implements ContainerExceptionInterface
{

}
