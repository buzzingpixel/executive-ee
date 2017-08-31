# Out of the Box Commands

## runSchedule

`php ee executive runSchedule`

This will run any commands that you have scheduled to run in your config file, or other add-on developers have scheduled to run on their add-on.

## makeCommand

`php ee executive makeCommand --description=MyCommandDescription`

Makes a command class skeleton class in the `system/user/Command` directory for you to write a custom command for your site.

## makeMigration

`php ee executive makeMigration --description=MyMigrationDescription`

Makes a migration skeleton class in `system/user/Migration` for you to write a custom migration for your site.

## runMigrations

`php ee executive runMigrations`

Runs any user migrations that have not been run yet. As long as you are using the command line tool to create the migrations so that they are named in proper sequence, they will be run in the order they were created.

## runAddonUpdates

`php ee executive runMigrations`

This will run updates for any add-ons that need to be updated.

## runAddonUpdateMethod

`php ee executive runAddonUpdateMethod --addon=addon_name`

Run's an add-on's `update` method in the add-on's `upd` file/class.

## getConfig

`php ee executive getConfig --key=expressionengine --index=database`

Get any ExpressionEngine config item. The `--index=item` argument is optional.

## clearCaches

`php ee executive clearCaches`

Clears ExpressionEngine caches.  Optionally specify type: --type=page (default: "all")
