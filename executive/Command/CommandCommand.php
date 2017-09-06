<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\Command;

use BuzzingPixel\Executive\Abstracts\BaseCommand;

/**
 * Class CommandCommand
 */
class CommandCommand extends BaseCommand
{
    /** @var string $dir */
    private $dir;

    /**
     * Initialize
     */
    public function initCommand()
    {
        $path = realpath(SYSPATH);
        $this->dir = "{$path}/user/Command";

        if (is_dir($this->dir)) {
            return;
        }

        if (! @mkdir($this->dir, DIR_WRITE_MODE, true) &&
            ! is_dir($this->dir)
        ) {
            $this->consoleService->writeLn(
                lang('unableToCreateDirectory:') . ' ' . $this->dir,
                'red'
            );
        }
    }

    /**
     * Make a command
     * @param string $description
     */
    public function make($description)
    {
        if (! $description) {
            $this->consoleService->writeLn(
                lang('migrationDescriptionRequired'),
                'red'
            );
            return;
        }

        $description = ucfirst($description);
        $rev = strrev($description);

        if (stripos($rev, strrev('Command')) === 0) {
            $description = strrev(substr($rev, 7));
        }

        // Get the template contents
        $contents = file_get_contents(
            EXECUTIVE_PATH . '/Template/Command.php'
        );

        // Replace things
        $contents = str_replace(
            array(
                'Class Command',
                'class Command',
            ),
            array(
                "Class {$description}Command",
                "class {$description}Command",
            ),
            $contents
        );

        $fullFilePath = "{$this->dir}/{$description}Command.php";

        if (file_exists($fullFilePath)) {
            $this->consoleService->writeLn(lang('fileExists'), 'red');
            $this->consoleService->writeLn($fullFilePath, 'red');
            return;
        }

        file_put_contents($fullFilePath, $contents);

        $this->consoleService->writeLn(
            lang('commandCreatedSuccessfully:') . ' ' . $fullFilePath,
            'green'
        );
    }
}
