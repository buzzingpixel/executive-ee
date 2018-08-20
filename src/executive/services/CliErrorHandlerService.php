<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services;

use EE_Lang;
use Throwable;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class CliErrorHandlerService
 */
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

    /** @var ConsoleOutput $consoleOutput */
    private $consoleOutput;

    /** @var EE_Lang $lang */
    private $lang;

    /**
     * ComposerProvisionCommand constructor
     * @param ConsoleOutput $consoleOutput
     * @param EE_Lang $lang
     */
    public function __construct(
        ConsoleOutput $consoleOutput,
        EE_Lang $lang
    ) {
        $this->consoleOutput = $consoleOutput;
        $this->lang = $lang;
    }

    /**
     * Runs the installation
     */
    public function register(): void
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
     * @return bool
     */
    public function shutdownHandler(): bool
    {
        if (@\is_array($error = @error_get_last())) {
            $this->errorHandler($error);
            return true;
        }

        return true;
    }

    /**
     * @param Throwable $e
     */
    public function exceptionHandler($e): void
    {
        $this->errorHandler(
            'Exception',
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );

        $args = EXECUTIVE_RAW_ARGS;
        $args = \is_array($args) ? $args : [];

        if (! \in_array('--trace=true', $args, true)) {
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
     * @param $type
     * @param $message
     * @param $file
     * @param $line
     */
    public function errorHandler(
        $type,
        $message = null,
        $file = null,
        $line = null
    ): void {
        if (! error_reporting()) {
            return;
        }

        $name = null;

        if ($type === 'Exception') {
            $name = 'Exception';
        }

        if (! $name && ! @\is_string(
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
