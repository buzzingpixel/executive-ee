<?php

/**
 * Class Migration
 */
class Migration
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
        $this->rootDir = $rootDir;
        if (isset($args[0])) {
            $this->name = ucfirst($args[0]);
        }
    }

    /**
     * Make the migration
     */
    public function make()
    {
        // Make sure we have a migration description
        if (! $this->name) {
            echo "\033[31mA migration description must be provided\n";
            return;
        }

        if (! is_dir("{$this->rootDir}/executive/Migration")) {
            mkdir("{$this->rootDir}/executive/Migration");
        }

        // Get the date
        $date = new DateTime();

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
        $className .= "_{$this->name}";

        // Get the template contents
        $contents = file_get_contents(
            $this->rootDir . '/Templates/Migration.php'
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

        // Place the file
        file_put_contents(
            "{$this->rootDir}/executive/Migration/{$className}.php",
            $contents
        );

        // Set console output
        echo "\033[32mexecutive/Migration/{$className}.php created\n";
    }
}
