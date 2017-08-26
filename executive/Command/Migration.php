<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

namespace BuzzingPixel\Executive\Command;

use BuzzingPixel\Executive\BaseComponent;
use BuzzingPixel\Executive\Service\ConsoleService;

/**
 * Class Service
 */
class Migration extends BaseComponent
{
    /** @var ConsoleService $consoleService */
    private $consoleService;

    /** @var string $dir */
    private $dir;

    /**
     * Initialize
     */
    public function init()
    {
        $this->consoleService = ee('executive:ConsoleService');
        $path = realpath(SYSPATH);
        $this->dir = "{$path}/user/Migration";
        if (! @mkdir($this->dir, DIR_WRITE_MODE, true) && ! is_dir($this->dir)) {
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
                lang('migrationDescriptionRequired'),
                'red'
            );
            return;
        }

        // Get the date
        $date = new \DateTime();

        // Create a class name
        $className = "m{$date->format('Y_m_d_Gis')}";
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
