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

## migrationStatus

`php ee executive migrationStatus`

Lists the migrations status. Requires [migrations configuration](migrations.md).

## runMigrations

`php ee executive runMigrations`

Runs any migrations that have not been run yet. As long as you are using the command line tool to create the migrations so that they are named in proper sequence, they will be run in the order they were created.

## reverseMigrations

`php ee executive reverseMigrations`

Reverses specified migrations. You will be asked which migrations you want to reverse and can specify the last migration, all migrations, or to a specific migration target.

## runAddonUpdateMethod

`php ee executive runAddonUpdateMethod --addon=addon_name`

Run's an add-on's `update` method in the add-on's `upd` file/class.

## runAddonUpdates

`php ee executive runAddonUpdates`

This will run updates for any add-ons that need to be updated.

## runQueue

`php ee executive runQueue`

This runs the next command in the [queue](queue.md). This should be run by something like Supervisor every second to continually process the queue as items are added to it.

## runSchedule

`php ee executive runSchedule`

This will run any commands that you have [scheduled](schedule.md) to run in your config file, or other add-on developers have scheduled to run on their add-ons.

## syncTemplates

`php ee executive syncTemplates`

This will sync your filesystem templates to the database. Here are the things this command does:

1. Makes sure all template groups in the filesystem have a index.html template present. This template exists whether you want it to or not so if one doesn't exist yet, Executive assumes you created a new template group and template in that group that is not index.html. The template Executive creates contains a 404 redirect tag to make sure an endpoint you didn't mean to create is not accessible
2. Deletes all Template Variables not present in the filesystem from the database
3. Deletes all Template Paritials not present in the filesystem from the database
4. Deletes all Templates not present in the filesystem from the database
5. Runs EE's internal methods to ensure the database versions of the templates are synced up from the filesystem
