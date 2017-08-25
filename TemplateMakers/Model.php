<?php

/**
 * Class Model
 */
class Model
{
    /** @var string $rootDir */
    private $rootDir;

    /** @var string $name */
    private $name;

    /**
     * Migration constructor
     * @param string $rootDir
     * @param array $args
     */
    public function __construct($rootDir, $args)
    {
        $name = '';

        $this->rootDir = $rootDir;

        if (isset($args[0])) {
            $name = ucfirst($args[0]);
        }

        $rev = strrev($name);

        if (stripos($rev, 'ledoM') === 0) {
            $name = strrev(substr($rev, 5));
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
            echo "\033[31mA model name must be provided\n";
            return;
        }

        // Set the full file path
        $fullFilePath = "{$this->rootDir}/executive/Model";
        if (! is_dir($fullFilePath)) {
            mkdir($fullFilePath);
        }
        $fullFilePath .= "/{$this->name}Model.php";

        // Make sure we won't overwrite anything
        if (file_exists($fullFilePath)) {
            echo "\033[31mA model with this name already exists\n";
            return;
        }

        // Get the template contents
        $contents = file_get_contents(
            $this->rootDir . '/Templates/Model.php'
        );

        // Replace things
        $contents = str_replace(
            array(
                'Class Model',
                'class Model',
            ),
            array(
                "Class {$this->name}Model",
                "class {$this->name}Model",
            ),
            $contents
        );

        // Place the file
        file_put_contents($fullFilePath, $contents);

        // Set console output
        echo "\033[32m{$fullFilePath}\n";
    }
}
