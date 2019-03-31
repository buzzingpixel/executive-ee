<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services;

use EE_Lang;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function array_flip;
use function array_search;
use function error_get_last;
use function error_reporting;
use function in_array;
use function ini_set;
use function is_array;
use function is_string;
use function print_r;
use function register_shutdown_function;
use function set_error_handler;
use function set_exception_handler;

class CliErrorHandlerService
{
    private const ERROR_CODES = [
        0x0001 => 'E_ERROR',
        0x0002 => 'E_WARNING',
        0x0004 => 'E_PARSE',
        0x0008 => 'E_NOTICE',
        0x0010 => 'E_CORE_ERROR',
        0x0020 => 'E_CORE_WARNING',
        0x0040 => 'E_COMPILE_ERROR',
        0x0080 => 'E_COMPILE_WARNING',
        0x0100 => 'E_USER_ERROR',
        0x0200 => 'E_USER_WARNING',
        0x0400 => 'E_USER_NOTICE',
        0x0800 => 'E_STRICT',
        0x1000 => 'E_RECOVERABLE_ERROR',
        0x2000 => 'E_DEPRECATED',
        0x4000 => 'E_USER_DEPRECATED',
    ];

    /** @var OutputInterface $consoleOutput */
    private $consoleOutput;
    /** @var EE_Lang $lang */
    private $lang;

    /**
     * CliErrorHandlerService constructor
     */
    public function __construct(
        OutputInterface $consoleOutput,
        EE_Lang $lang
    ) {
        $this->consoleOutput = $consoleOutput;
        $this->lang          = $lang;
    }

    /**
     * Registers error handling for the CLI
     */
    public function register() : void
    {
        ini_set('display_errors', 'On');
        ini_set('html_errors', '0');
        error_reporting(-1);

        register_shutdown_function([$this, 'shutdownHandler']);

        set_exception_handler([$this, 'exceptionHandler']);

        set_error_handler([$this, 'errorHandler']);
    }

    /**
     * Handles PHP Shutdown
     */
    public function shutdownHandler() : bool
    {
        $error = @error_get_last();

        if (@is_array($error)) {
            $this->errorHandler($error);

            return true;
        }

        return true;
    }

    public function exceptionHandler(Throwable $e) : void
    {
        $this->errorHandler(
            'Exception',
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );

        $args = EXECUTIVE_RAW_ARGS;
        $args = is_array($args) ? $args : [];

        if (! in_array('--trace=true', $args, true)) {
            $this->consoleOutput->writeln(
                '<fg=yellow>' .
                $this->lang->line('getTrace') .
                '</>'
            );

            return;
        }

        print_r($e->getTrace());
    }

    /**
     * Handles errors
     */
    public function errorHandler(
        $type,
        $message = null,
        $file = null,
        $line = null
    ) : void {
        if (! error_reporting()) {
            return;
        }

        $name = null;

        if ($type === 'Exception') {
            $name = 'Exception';
        }

        if (! $name && ! @is_string(
            $name = @array_search(
                $type,
                @array_flip(
                    self::ERROR_CODES
                )
            )
        )) {
            $name = 'E_UNKNOWN';
        }

        $this->consoleOutput->writeln(
            '<fg=red;options=bold>' .
            $this->lang->line('followingErrorEncountered') .
            '</>'
        );

        $this->consoleOutput->writeln(
            '<fg=red>' .
            $name . ': ' . $message .
            '</>'
        );

        $this->consoleOutput->writeln(
            '<fg=red>' .
            'File: ' . $file .
            '</>'
        );

        $this->consoleOutput->writeln(
            '<fg=red>' .
            'Line: ' . $line .
            '</>'
        );
    }
}
