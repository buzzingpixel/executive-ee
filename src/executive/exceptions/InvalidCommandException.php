<?php

declare(strict_types=1);

namespace buzzingpixel\executive\exceptions;

use Exception;
use Throwable;

class InvalidCommandException extends Exception
{
    /**
     * InvalidCommandException constructor
     */
    public function __construct(
        string $message = '',
        int $code = 500,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
