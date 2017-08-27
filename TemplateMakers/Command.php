<?php

/**
 * Class Command
 */
class Command
{
    /** @var string $rootDir */
    private $rootDir;

    /** @var string $name */
    private $name;

    /** @var string $directory */
    private $directory;

    /**
     * Migration constructor
     * @param string $rootDir
     * @param array $args
     */
    public function __construct($rootDir, $args)
    {
        $this->rootDir = $rootDir;

        $name = '';

        if (isset($args[1])) {
            $this->directory = ucfirst($args[0]);
            $name = ucfirst($args[1]);
        } elseif (isset($args[0])) {
            $name = ucfirst($args[0]);
        }

        $rev = strrev($name);

        if (stripos($rev, strrev('Command')) === 0) {
            $name = strrev(substr($rev, 7));
        }

        $this->name = $name;
    }

    /**
     * Make the controller
     */
    public function make()
    {
        // Make sure we have a controller name
        if (! $this->name) {
            echo "\033[31mA command name must be provided\n";
            return;
        }

        // Set the full file path
        $fullFilePath = "{$this->rootDir}/executive/Command";
        if (! is_dir($fullFilePath)) {
            mkdir($fullFilePath);
        }
        if ($this->directory) {
            $fullFilePath .= "/{$this->directory}";
            if (! is_dir($fullFilePath)) {
                mkdir($fullFilePath);
            }
        }
        $fullFilePath .= "/{$this->name}Command.php";

        // Make sure we won't overwrite anything
        if (file_exists($fullFilePath)) {
            echo "\033[31mA command with this name already exists\n";
            return;
        }

        // Get the template contents
        $contents = file_get_contents(
            $this->rootDir . '/Templates/Command.php'
        );

        // Replace things
        $contents = str_replace(
            array(
                'Class Command',
                'class Command',
            ),
            array(
                "Class {$this->name}Command",
                "class {$this->name}Command",
            ),
            $contents
        );

        // If a directory is specified, set namespace
        if ($this->directory) {
            $contents = str_replace(
                'BuzzingPixel\Executive\Command',
                "BuzzingPixel\Executive\Command\\{$this->directory}",
                $contents
            );
        }

        // Make a directory if necessary
        if ($this->directory) {
            $dirPath = "{$this->rootDir}/executive/Command/{$this->directory}";
            if (! is_dir($dirPath)) {
                mkdir($dirPath);
            }
        }

        // Place the file
        file_put_contents($fullFilePath, $contents);

        // Set console output
        echo "\033[32m{$fullFilePath}\n";
    }
}
