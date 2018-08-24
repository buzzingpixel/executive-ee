# Out of the Box Commands

## clearCaches

`php ee executive clearCaches`

Clears ExpressionEngine caches.  Optionally specify type: --type=page (default: "all").

## composerProvision

`php ee executive composerProvision`

Creates symlinks for EE and/or addons installed via Composer. Learn more about this command with the [more in-depth documentation](composer-provisioning.md).

## getConfig

`php ee executive getConfig --key=expressionengine --index=database`

Get any ExpressionEngine config item. The `--index=item` argument is optional.

## listMigrations

`php ee executive listMigrations`

Lists migrations that have not yet been run. Requires [migrations configuration](migrations.md).

## makeFromTemplate

`php ee executive makeFromTemplate`

Makes a class from a template based on user config. Config example:

```php
$config['classTemplateConfigurations'] = [
    'service' => [
        'namespace' => 'myapp\services', // required
        'destination' => APP_DIR . '/src/services', // required
        'classNameSuffix' => 'Service', // optional
        'templateLocation' => APP_DIR . '/src/services/Template.php', // optional, defaults to Executive's sample template
        'classNameToReplace' => 'Template', // optional
    ],
];
```

## makeMigration

`php ee executive makeMigration`

Make a migration class. Requires [migrations configuration](migrations.md).

## runMigrations

`php ee executive runMigrations`

Runs any migrations that have not been run yet. As long as you are using the command line tool to create the migrations so that they are named in proper sequence, they will be run in the order they were created.

## runAddonUpdateMethod

`php ee executive runAddonUpdateMethod --addon=addon_name`

Run's an add-on's `update` method in the add-on's `upd` file/class.

## runAddonUpdates

`php ee executive runAddonUpdates`

This will run updates for any add-ons that need to be updated.

## runSchedule

`php ee executive runSchedule`

This will run any commands that you have [scheduled](schedule.md) to run in your config file, or other add-on developers have scheduled to run on their add-ons.
