<?php

declare(strict_types=1);

use buzzingpixel\executive\commands\AddOnUpdatesCommand;
use buzzingpixel\executive\commands\CacheCommand;
use buzzingpixel\executive\commands\ComposerProvisionCommand;
use buzzingpixel\executive\commands\ConfigCommand;
use buzzingpixel\executive\commands\InstallExecutiveCommand;
use buzzingpixel\executive\commands\ListUserMigrationsCommand;
use buzzingpixel\executive\commands\MakeFromTemplateCommand;
use buzzingpixel\executive\commands\MakeMigrationCommand;
use buzzingpixel\executive\commands\ReverseUserMigrationsCommand;
use buzzingpixel\executive\commands\RunQueueCommand;
use buzzingpixel\executive\commands\RunScheduleCommand;
use buzzingpixel\executive\commands\RunUserMigrationsCommand;
use BuzzingPixel\Executive\commands\ShowMigrationStatus;
use buzzingpixel\executive\commands\SyncTemplatesCommand;
use buzzingpixel\executive\ExecutiveDi;
use Composer\Package\CompletePackageInterface;
use EllisLab\ExpressionEngine\Core\Provider;

// Edge case and weirdness with composer
getenv('HOME') || putenv('HOME=' . APP_DIR);

$composerApp = new Composer\Console\Application();
$oldCwd      = getcwd();
chdir(APP_DIR);
/** @noinspection PhpUnhandledExceptionInspection */
$composer                      = $composerApp->getComposer();
$repositoryManager             = $composer->getRepositoryManager();
$installedFilesystemRepository = $repositoryManager->getLocalRepository();
/** @var CompletePackageInterface $executive */
$executive = $installedFilesystemRepository->findPackage(
    'buzzingpixel/executive-ee',
    '*'
);

$author = $executive->getAuthors()[0];
$extra  = $executive->getExtra();

chdir($oldCwd);

// Define constants
defined('EXECUTIVE_NAME') || define('EXECUTIVE_NAME', 'Executive');
defined('EXECUTIVE_VER') || define('EXECUTIVE_VER', $extra['version']);
defined('EXECUTIVE_PATH') || define('EXECUTIVE_PATH', realpath(__DIR__));
defined('EXECUTIVE_MIGRATION_FILES_PATH') ||
    define('EXECUTIVE_MIGRATION_FILES_PATH', __DIR__ . '/migrations');

// We need to check if an installation is being requested
// Also, we need to make sure EE loads our lang file at all times
if (defined('REQ') && REQ === 'CONSOLE') {
    // Load the dang lang file, EE
    ee()->lang->loadfile('executive');

    /** @noinspection PhpUnhandledExceptionInspection */
    $exit = ExecutiveDi::diContainer()->get(InstallExecutiveCommand::class)->run();

    if ($exit) {
        exit;
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
        'Provider' => static function (Provider $provider) {
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
        'reverseMigrations' => [
            'class' => ReverseUserMigrationsCommand::class,
            'method' => 'reverseMigrations',
            'description' => lang('reverseMigrationsDescription'),
        ],
        'migrationStatus' => [
            'class' => ShowMigrationStatus::class,
            'method' => 'showMigrationStatus',
            'description' => lang('showMigrationStatusDescription'),
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
        'runQueue' => [
            'class' => RunQueueCommand::class,
            'method' => 'run',
            'description' => lang('runQueueDescription'),
        ],
        'runSchedule' => [
            'class' => RunScheduleCommand::class,
            'method' => 'run',
            'description' => lang('runScheduleDescription'),
        ],
        'syncTemplates' => [
            'class' => SyncTemplatesCommand::class,
            'method' => 'run',
            'description' => lang('runSyncTemplatesDescription'),
        ],
    ],
];
