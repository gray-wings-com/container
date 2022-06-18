<?php
declare(strict_types=1);

namespace Graywings\Container\Exceptions;

use Throwable;
use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
    public function __construct(
        string $message = '',
        int $code = 0,
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