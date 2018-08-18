<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\Command;

use BuzzingPixel\Executive\Abstracts\BaseCommand;

/**
 * Class Service
 */
class MigrationCommand extends BaseCommand
{
    /** @var string $dir */
    private $dir;

    /**
     * Initialize
     */
    public function initCommand()
    {
        $path = realpath(SYSPATH);
        $this->dir = "{$path}/user/Migration";

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
     * Make a migration
     * @param string $description
     */
    public function make($description)
    {
        if (! $description) {
            $this->consoleService->writeLn(
                lang('templateDescriptionRequired'),
                'red'
            );
            return;
        }

        // Get the date
        $date = new \DateTime();

        // Create a class name
        $className = "m{$date->format('Y_m_d_His')}";
        $first = true;
        foreach (range(strlen($className), 18) as $key => $val) {
            if ($first) {
                $first = false;
                continue;
            }
            $className .= '0';
        }
        $className .= "_{$description}";

        // Get the template contents
        $contents = file_get_contents(
            EXECUTIVE_PATH . '/Template/Migration.php'
        );

        // Replace things
        $contents = str_replace(
            array(
                'Class Migration',
                'class Migration',
            ),
            array(
                "Class {$className}",
                "class {$className}",
            ),
            $contents
        );

        $fullFilePath = "{$this->dir}/{$className}.php";

        // Place the file
        file_put_contents($fullFilePath, $contents);

        $this->consoleService->writeLn(
            lang('migrationCreatedSuccessfully:') . ' ' . $fullFilePath,
            'green'
        );
    }
}
