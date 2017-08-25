<?php

/**
 * Class Service
 */
class Service
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

        if (stripos($rev, 'ecivreS') === 0) {
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
            echo "\033[31mA service name must be provided\n";
            return;
        }

        // Set the full file path
        $fullFilePath = "{$this->rootDir}/executive/Service";
        if (! is_dir($fullFilePath)) {
            mkdir($fullFilePath);
        }
        if ($this->directory) {
            $fullFilePath .= "/{$this->directory}";
            if (! is_dir($fullFilePath)) {
                mkdir($fullFilePath);
            }
        }
        $fullFilePath .= "/{$this->name}Service.php";

        // Make sure we won't overwrite anything
        if (file_exists($fullFilePath)) {
            echo "\033[31mA service with this name already exists\n";
            return;
        }

        // Get the template contents
        $contents = file_get_contents(
            $this->rootDir . '/Templates/Service.php'
        );

        // Replace things
        $contents = str_replace(
            array(
                'Class Service',
                'class Service',
            ),
            array(
                "Class {$this->name}Service",
                "class {$this->name}Service",
            ),
            $contents
        );

        // If a directory is specified, set namespace
        if ($this->directory) {
            $contents = str_replace(
                'BuzzingPixel\Executive\Service',
                "BuzzingPixel\Executive\Service\\{$this->directory}",
                $contents
            );
        }

        // Make a directory if necessary
        if ($this->directory) {
            $dirPath = "{$this->rootDir}/executive/Service/{$this->directory}";
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
