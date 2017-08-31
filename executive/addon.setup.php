<?php // @codingStandardsIgnoreStart

// @codingStandardsIgnoreEnd

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use BuzzingPixel\Executive\Controller\ConsoleController;
use BuzzingPixel\Executive\Service\ArgsService;
use BuzzingPixel\Executive\Service\ConsoleService;
use BuzzingPixel\Executive\Service\CommandsService;

// Get addon json path
$addOnPath = realpath(__DIR__);
$addOnJsonPath = "{$addOnPath}/addon.json";

// Get the addon json file
$addOnJson = json_decode(file_get_contents($addOnJsonPath));

// Set paths
$sysPath = rtrim(realpath(SYSPATH), '/') . '/';
$cachePath = "{$sysPath}user/cache/";

// Get vendor autoload
$vendorAutoloadFile = "{$addOnPath}/vendor/autoload.php";

// Require the autoload file if path exists
if (file_exists($vendorAutoloadFile)) {
    require $vendorAutoloadFile;
}

// Define constants
defined('EXECUTIVE_NAME') || define('EXECUTIVE_NAME', $addOnJson->label);
defined('EXECUTIVE_VER') || define('EXECUTIVE_VER', $addOnJson->version);
defined('EXECUTIVE_PATH') || define('EXECUTIVE_PATH', realpath(__DIR__));
defined('EXECUTIVE_MIGRATION_FILES_PATH') ||
    define('EXECUTIVE_MIGRATION_FILES_PATH', __DIR__ . '/Migration');



// Add an auto loader for user classes
spl_autoload_register(function ($class) {
    // Break up the class into an array
    $ns = explode('\\', $class);

    // Check if this is for us
    if ($ns[0] !== 'User' || ! isset($ns[1])) {
        return;
    }

    // Unset invalid parts of the namespace
    unset($ns[0]);

    // Put the path to the class file together
    $sysPath = SYSPATH;
    $sep = DIRECTORY_SEPARATOR;
    $ns = implode($sep, $ns);
    $path = "{$sysPath}user{$sep}{$ns}.php";

    // Load the file if it exists
    if (file_exists($path)) {
        include_once $path;
    }
});

// Check if this is a console request
if (defined('REQ') && REQ === 'CONSOLE') {
    // Make sure lang file is loaded
    ee()->lang->loadfile('executive');

    // Get arguments sent to command line
    $args = EXECUTIVE_RAW_ARGS;

    // Run query to check if Executive is installed
    /** @var \EllisLab\ExpressionEngine\Service\Database\Query $queryBuilder */
    $queryBuilder = ee('db');
    $query = (int) $queryBuilder->where('module_name', 'Executive')
        ->get('modules')
        ->num_rows();

    // If Executive is not installed
    if ($query < 1) {
        // If command line is not requesting an installation, warn user
        if (isset($args[2]) ||
            count($args) > 2 ||
            ! isset($args[1]) ||
            $args[1] !== 'install'
        ) {
            $lang = lang('notInstalled');
            exit("\033[31m{$lang}\n");
        }

        // Manually include dependencies
        include_once __DIR__ . '/upd.executive.php';
        include_once __DIR__ . '/BaseComponent.php';
        include_once __DIR__ . '/Abstracts/BaseMigration.php';
        include_once __DIR__ . '/Controller/MigrationController.php';

        foreach (glob(__DIR__ . '/Migration/*') as $file) {
            $pathInfo = pathinfo($file);
            if (! isset($pathInfo['extension']) ||
                $pathInfo['extension'] !== 'php'
            ) {
                continue;
            }
            include_once $file;
        }

        // Get the executive installer
        $installer = new Executive_upd();

        // Run the installer
        $installer->install();

        // Show user message
        $lang = lang('executiveInstalled');
        exit("\033[32m{$lang}\n");
    }
}

// Return info about the add on for ExpressionEngine
return array(
    'author' => $addOnJson->author,
    'author_url' => $addOnJson->authorUrl,
    'description' => $addOnJson->description,
    'docs_url' => $addOnJson->docsUrl,
    'name' => $addOnJson->label,
    'namespace' => $addOnJson->namespace,
    'settings_exist' => $addOnJson->settingsExist,
    'version' => $addOnJson->version,
    'services' => array(
        /**
         * Controllers
         */
        'ConsoleController' => function () {
            return new ConsoleController();
        },

        /**
         * Services
         */
        'ArgsService' => function () {
            return new ArgsService();
        },
        'ConsoleService' => function () {
            return new ConsoleService();
        },
        'CommandsService' => function () {
            return new CommandsService(array(
                'eeAddonFactory' => ee('Addon'),
                'eeConfigService' => ee()->config,
                'consoleService' => ee('executive:ConsoleService'),
                'queryBuilder' => ee('db'),
            ));
        }
    ),
    'commands' => array(
        'runSchedule' => array(
            'class' => '\BuzzingPixel\Executive\Command\ScheduleCommand',
            'method' => 'run',
            'description' => lang('runScheduleDescription'),
        ),
        'makeCommand' => array(
            'class' => '\BuzzingPixel\Executive\Command\CommandCommand',
            'method' => 'make',
            'description' => lang('makeCommandDescription'),
        ),
        'makeMigration' => array(
            'class' => '\BuzzingPixel\Executive\Command\MigrationCommand',
            'method' => 'make',
            'description' => lang('makeMigrationDescription'),
        ),
        'runMigrations' => array(
            'class' => '\BuzzingPixel\Executive\Command\UserMigrationCommand',
            'method' => 'runMigrations',
            'description' => lang('runMigrationsDescription'),
        ),
        'runAddonUpdates' => array(
            'class' => '\BuzzingPixel\Executive\Command\AddonUpdatesCommand',
            'method' => 'run',
            'description' => lang('runAddonUpdatesDescription'),
        ),
        'runAddonUpdateMethod' => array(
            'class' => '\BuzzingPixel\Executive\Command\AddonUpdatesCommand',
            'method' => 'runAddonUpdateMethod',
            'description' => lang('runAddonUpdateMethodDescription'),
        ),
        'getConfig' => array(
            'class' => '\BuzzingPixel\Executive\Command\ConfigCommand',
            'method' => 'get',
            'description' => lang('getConfigDescription'),
        ),
        'clearCaches' => array(
            'class' => '\BuzzingPixel\Executive\Command\CacheCommand',
            'method' => 'clearCaches',
            'description' => lang('clearCachesDescription'),
        ),
    ),
);
