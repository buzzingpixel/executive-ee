<?php // @codingStandardsIgnoreStart
declare(strict_types=1);

// @codingStandardsIgnoreEnd

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use buzzingpixel\executive\ExecutiveDi;
use EllisLab\ExpressionEngine\Core\Provider;
use Composer\Package\CompletePackageInterface;
use buzzingpixel\executive\commands\CacheCommand;
use buzzingpixel\executive\commands\ConfigCommand;
use buzzingpixel\executive\commands\RunScheduleCommand;
use buzzingpixel\executive\commands\AddOnUpdatesCommand;
use buzzingpixel\executive\commands\MakeMigrationCommand;
use buzzingpixel\executive\commands\MakeFromTemplateCommand;
use buzzingpixel\executive\commands\InstallExecutiveCommand;
use buzzingpixel\executive\commands\ComposerProvisionCommand;
use buzzingpixel\executive\commands\RunUserMigrationsCommand;
use buzzingpixel\executive\commands\ListUserMigrationsCommand;

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
        'Provider' => function (Provider $provider) {
            return $provider;
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
        'listMigrations' => [
            'class' => ListUserMigrationsCommand::class,
            'method' => 'run',
            'description' => lang('listMigrationsDescription'),
        ],
        'makeFromTemplate' => [
            'class' => MakeFromTemplateCommand::class,
            'method' => 'make',
            'description' => lang('makeClassDescription'),
        ],
        'makeMigration' => [
            'class' => MakeMigrationCommand::class,
            'method' => 'make',
            'description' => lang('makeMigrationDescription'),
        ],
        'runMigrations' => [
            'class' => RunUserMigrationsCommand::class,
            'method' => 'runMigrations',
            'description' => lang('runMigrationsDescription'),
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
            'class' => RunScheduleCommand::class,
            'method' => 'run',
            'description' => lang('runScheduleDescription'),
        ],
    ],
];
