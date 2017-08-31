<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

// @codingStandardsIgnoreStart

$fileName = 'ee';

if (defined('REQ') && REQ === 'CONSOLE') {
    $fileName = basename($_SERVER['SCRIPT_FILENAME'], '.php');
}

$lang = array(
    'notInstalled' => 'Executive is not installed. Please run "php ' . $fileName . ' install"',
    'executiveInstalled' => 'Executive has been installed',
    'usage:' => 'usage:',
    'usageExample' => 'php ' . $fileName . ' [group] [command] [--argument=value] [--argument2=value2]',
    'executiveCommandLine' => 'Executive Command Line',
    'group:' => 'Group:',
    'mustSpecifyCommand' => 'A command must be specified',
    'commandNotFound' => 'The specified command was not found',
    'classNotFound' => 'The specified class was not found',
    'classMethodNotFound' => 'The specified class method was not found',
    'unableToCreateDirectory:' => 'Unable to create directory:',
    'migrationDescriptionRequired' => 'A migration description is required: --description=MyMigration',
    'migrationCreatedSuccessfully:' => 'Migration created successfully:',
    'addonsUpdatedSuccessfully' => 'Addons updated successfully',
    'keyMustBeSpecified' => 'A key must be specified: --key=debug',
    'addonMustBeSpecified:' => 'An addon must be specified:',
    'addonNotFound' => 'That addon was not found',
    'addonNotInstalled' => 'That addon is not installed',
    'addonUpdateRunSuccessfully' => 'Addon update run successfully',
    'noMigrationsToRun' => "You're up to date! There are no migrations to run.",
    'followingMigrationsRun' => 'The following migrations were run:',
    'makeMigrationDescription' => 'Create a migration skeleton class in "system/user/Migration"',
    'runMigrationsDescription' => 'Run any migrations in the "user/Migration" directory that need to be run',
    'runAddonUpdatesDescription' => 'Run all addon updates',
    'runAddonUpdateMethodDescription' => "Run an add-on's update method: --addon=addon_name",
    'getConfigDescription' => 'Get config item: --key=expressionengine --index=database',
    'clearCachesDescription' => 'Clear caches. Optionally specify type: --type=page (default: "all")',
    'cachesCleared' => 'Caches cleared',
    'runScheduleDescription' => 'Run scheduled commands. You can (and should) run this command every minute on a cron.',
    'isCurrentlyRunning' => 'is currently running',
    'notRunYet' => 'does not need run at this time',
    'anErrorOccurredRunningCommand:' => 'An error occurred running the command:',
    'ranSuccessfully' => 'ran successfully',
    'noScheduledCommands' => 'There are no scheduled commands set up',
    'followingExceptionCaught' => 'The following exception was caught',
    'getTrace' => 'To get the backtrace, use the option: --trace=true',
);
