<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

// @codingStandardsIgnoreStart

$fileName = SELF;

if (defined('REQ') && REQ === 'CONSOLE') {
    $fileName = basename($_SERVER['SCRIPT_FILENAME'], '.php');
}

$lang = [
    'notInstalled' => 'Executive is not installed. Please run "php ' . $fileName . ' install"',
    'executiveInstalled' => 'Executive has been installed',
    'usage:' => 'usage:',
    'usageExample' => 'php ' . $fileName . ' [group] [command] [--argument=value] [--argument2=value2]',
    'executiveCommandLine' => 'Executive Command Line',
    'group:' => 'Group:',
    'mustSpecifyCommand' => 'A command must be specified',
    'groupNotFound' => 'The specified group was not found',
    'commandNotFound' => 'The specified command was not found',
    'classNotFound' => 'The specified class was not found',
    'classMethodNotFound' => 'The specified class method was not found',
    'unableToCreateDirectory:' => 'Unable to create directory:',
    'templateDescriptionRequired' => 'A description is required: --description=MyDescription',
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
    'runMigrationsDescription' => 'Run any migrations in the specified user migrations directory that need to be run',
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
    'followingErrorEncountered' => 'The following error was encountered',
    'getTrace' => 'To get the backtrace, use the option: --trace=true',
    'commandCreatedSuccessfully:' => 'Command created successfully:',
    'makeCommandDescription' => 'Create a command skeleton class in "system/user/Command"',
    'fileExists' => 'That file already exists',
    'extClassRequired' => 'Extension class required',
    'extMethodRequired' => 'Extension method required',
    'extHookRequired' => 'Extension hook required',
    'extPriorityRequired' => 'Extension priority required',
    'tagCreatedSuccessfully:' => 'Tag created successfully:',
    'makeTagDescription' => 'Create a tag skeleton class in "system/user/Tag"',
    'noCpSections' => 'No user CP sections have been set up.',
    'userCpSections' => 'User CP Sections',
    'userCpSectionNotFound' => 'The requested User CP Section was not found.',
    'commandCallableNotSpecified' => "The command's callable was not found or not specified",
    'composerProvisionDescription' => 'Creates symlinks for EE and/or addons installed via Composer',
    'youMustProvideAValue' => 'You must provide a value',
    'addonShortName' => 'Add-on short name',
    'configKey' => 'Config key',
    'className' => 'Class name',
    'createFileAt' => 'Create file at',
    'aborting' => 'Aborting',
    'fileExistsAtDestination' => 'File already exists at destination',
    'unknownTemplateMakerError' => 'An unknown template maker error occurred',
    'cannotCreateTemplateDirectory' => 'An error occurred trying to create the template\'s destination directory',
    'specifyMakeMigrationNamespace' => 'You must specify $config[\'migrationNamespace\'] = \'\some\\name\space\' in the config file',
    'specifyMakeMigrationDestination' => 'You must specify $config[\'migrationDestination\'] = \'/path/to/dest/directory\' in the config file',
    'usingDefaultMigrationTemplate' => '$config[\'migrationTemplateLocation\'] has not been set in the config file so Executive\'s default template will be used',
    'migrationCreated' => 'Migration created',
    'sourceTemplateMissing' => 'The specified source template is missing',
    'noClassTemplateConfigurationsAvailable' => 'There are no template configurations available. Set with $config[\'classTemplateConfigurations\']',
    'makeClassDescription' => 'Makes a class from a template based on user config',
    'invalidTemplateConfigurationName' => 'One of your template configurations is missing its key. The key is used for the name of the configuration.',
    'invalidTemplateConfigurationNameSpace' => 'Your template configuration with the key of {{key}} is missing the namespace configuration',
    'invalidTemplateConfigurationDestination' => 'Your template configuration with the key of {{key}} is missing the destination configuration',
    'choseFromAvailableTemplateConfigs' => 'Choose from available template configs',
    'enterConfig' => 'Enter the config option you want to use',
    'invalidConfigOption' => 'The config option you have chosen is not available',
    'usingDefaultTemplate' => 'The template configuration has not set "templateLocation" so Executive\'s default template will be used',
    'usingDefaultClassNameToReplace' => 'The template configuration has not set "classNameToReplace" so Executive will search for and replace the class name "ClassTemplate"',
    'classCreated' => 'The class was created',
    'runningCommand' => 'Running command',
];
