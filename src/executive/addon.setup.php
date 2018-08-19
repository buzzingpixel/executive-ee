<?php // @codingStandardsIgnoreStart

// @codingStandardsIgnoreEnd

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use buzzingpixel\executive\ExecutiveDi;
use EllisLab\ExpressionEngine\Core\Provider;
use Composer\Package\CompletePackageInterface;
use BuzzingPixel\Executive\Service\ArgsService;
use BuzzingPixel\Executive\Service\ConsoleService;
use BuzzingPixel\Executive\Service\CommandsService;
use BuzzingPixel\Executive\Service\UserViewService;
use BuzzingPixel\Executive\Controller\ConsoleController;
use buzzingpixel\executive\commands\InstallExecutiveCommand;

$composerApp = new Composer\Console\Application();
$oldCwd = getcwd();
chdir(APP_DIR);
/** @noinspection PhpUnhandledExceptionInspection */
$composer = $composerApp->getComposer();
$repositoryManager = $composer->getRepositoryManager();
$installedFilesystemRepository = $repositoryManager->getLocalRepository();
/** @var CompletePackageInterface $executive */
$executive = $installedFilesystemRepository->findPackage(
    'buzzingpixel/executive-ee',
    '>0'
);

$author = $executive->getAuthors()[0];
$extra = $executive->getExtra();

chdir($oldCwd);

// Define constants
defined('EXECUTIVE_NAME') || define('EXECUTIVE_NAME', 'Executive');
defined('EXECUTIVE_VER') || define('EXECUTIVE_VER', $executive->getPrettyVersion());
defined('EXECUTIVE_PATH') || define('EXECUTIVE_PATH', realpath(__DIR__));
defined('EXECUTIVE_MIGRATION_FILES_PATH') ||
    define('EXECUTIVE_MIGRATION_FILES_PATH', __DIR__ . '/Migration');

// We need to check if an installation is being requested
// Also, we need to make sure EE loads our lang file at all times
if (defined('REQ') && REQ === 'CONSOLE') {
    // Load the dang lang file, EE
    ee()->lang->loadfile('executive');

    /** @noinspection PhpUnhandledExceptionInspection */
    $exit = ExecutiveDi::get(InstallExecutiveCommand::class)->run();

    if ($exit) {
        exit();
    }
}

// Return info about the add on for ExpressionEngine
return array(
    'author' => $author['name'],
    'author_url' => $author['homepage'],
    'description' => $executive->getDescription(),
    'docs_url' => 'https://buzzingpixel.com/software/executive-ee/documentation',
    'name' => EXECUTIVE_NAME,
    'namespace' => '\\',
    'settings_exist' => true,
    'version' => EXECUTIVE_VER,
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
        },
        'UserView' => function (Provider $addOn, $path = '') {
            return new UserViewService($path, $addOn);
        },
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
        'makeTag' => array(
            'class' => '\BuzzingPixel\Executive\Command\TagCommand',
            'method' => 'make',
            'description' => lang('makeTagDescription'),
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
