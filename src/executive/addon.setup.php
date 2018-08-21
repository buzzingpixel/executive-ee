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
use BuzzingPixel\Executive\Command\TagCommand;
use buzzingpixel\executive\commands\CacheCommand;
use buzzingpixel\executive\commands\ConfigCommand;
use BuzzingPixel\Executive\Service\ConsoleService;
use BuzzingPixel\Executive\Command\ScheduleCommand;
use BuzzingPixel\Executive\Service\CommandsService;
use BuzzingPixel\Executive\Service\UserViewService;
use BuzzingPixel\Executive\Command\MigrationCommand;
use buzzingpixel\executive\commands\MakeCommandCommand;
use buzzingpixel\executive\commands\AddOnUpdatesCommand;
use BuzzingPixel\Executive\Command\UserMigrationCommand;
use buzzingpixel\executive\commands\InstallExecutiveCommand;
use buzzingpixel\executive\commands\ComposerProvisionCommand;

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
    define('EXECUTIVE_MIGRATION_FILES_PATH', __DIR__ . '/migrations');

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
return [
    'author' => $author['name'],
    'author_url' => $author['homepage'],
    'description' => $executive->getDescription(),
    'docs_url' => 'https://buzzingpixel.com/software/executive-ee/documentation',
    'name' => EXECUTIVE_NAME,
    'namespace' => '\\',
    'settings_exist' => true,
    'version' => EXECUTIVE_VER,
    'services' => [
        /**
         * Services
         */
        'ConsoleService' => function () {
            return new ConsoleService();
        },
        'CommandsService' => function () {
            return new CommandsService([
                'eeAddonFactory' => ee('Addon'),
                'eeConfigService' => ee()->config,
                'consoleService' => ee('executive:ConsoleService'),
                'queryBuilder' => ee('db'),
            ]);
        },
        'UserView' => function (Provider $addOn, $path = '') {
            return new UserViewService($path, $addOn);
        },
    ],
    'commands' => [
        'clearCaches' => [
            'class' => CacheCommand::class,
            'method' => 'clearCaches',
            'description' => lang('clearCachesDescription'),
        ],
        'composerProvision' => [
            'class' => ComposerProvisionCommand::class,
            'method' => 'run',
            'description' => lang('composerProvisionDescription'),
        ],
        'getConfig' => [
            'class' => ConfigCommand::class,
            'method' => 'get',
            'description' => lang('getConfigDescription'),
        ],
        'makeCommand' => [
            'class' => MakeCommandCommand::class,
            'method' => 'make',
            'description' => lang('makeCommandDescription'),
        ],
        'makeMigration' => [
            'class' => MigrationCommand::class,
            'method' => 'make',
            'description' => lang('makeMigrationDescription'),
        ],
        'runMigrations' => [
            'class' => UserMigrationCommand::class,
            'method' => 'runMigrations',
            'description' => lang('runMigrationsDescription'),
        ],
        'makeTag' => [
            'class' => TagCommand::class,
            'method' => 'make',
            'description' => lang('makeTagDescription'),
        ],
        'runAddonUpdateMethod' => [
            'class' => AddOnUpdatesCommand::class,
            'method' => 'runAddonUpdateMethod',
            'description' => lang('runAddonUpdateMethodDescription'),
        ],
        'runAddonUpdates' => [
            'class' => AddOnUpdatesCommand::class,
            'method' => 'run',
            'description' => lang('runAddonUpdatesDescription'),
        ],
        'runSchedule' => [
            'class' => ScheduleCommand::class,
            'method' => 'run',
            'description' => lang('runScheduleDescription'),
        ],
    ],
];
