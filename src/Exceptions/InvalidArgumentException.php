<?php
declare(strict_types=1);

namespace Graywings\Container\Exceptions;

use Throwable;
use InvalidArgumentException as BaseException;
use Psr\Container\ContainerExceptionInterface;

class InvalidArgumentException extends BaseException implements ContainerExceptionInterface
{
    public function __construct(
        string    $message = '',
        int       $code = 0,
        Throwable $previous = null
    )
    {
        parent::__construct(
            $message,
            $code,
            $previous
        );
    }
}