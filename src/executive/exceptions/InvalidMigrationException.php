<?php
declare(strict_types=1);

namespace buzzingpixel\executive\exceptions;

use Exception;
use Throwable;

/**
 * Class InvalidMigrationException
 */
class InvalidMigrationException extends Exception
{
    /**
     * InvalidMigrationException constructor
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = '',
        int $code = 500,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
