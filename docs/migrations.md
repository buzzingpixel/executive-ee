# Migrations

One of the big power plays for Executive is Migrations. Migrations are classes with a single method that is run once when you `runMigrations`. Those of you who use Laravel or other applications with migrations can probably skip the details, but for clarity's sake:

You create a migration file/class and in the `safeUp` method, you write code that manipulates your database. Executive keeps track of the migrations classes that have already run, and runs any that haven't been run. Ideally you would have your deployment process set up to run the `runMigrations` command right after deploy. This allows you to make schema changes once, in code, and each environment get's the required schema changes when it gets out to that environment.

## Creating migrations

In your development environment, you would run the command:

`php ee executive makeMigration --description=MyMigrationDescription`

Then you edit the resulting migration file that was created to do your migrations (Executive provides access to some classes and interfaces to aid in these schema updates).

After you're done writing your migration class, in your development environment run:

`php ee executive runMigrations`

Your new migration will be run and your schema updated. You can then go on developing. When you're ready to deploy to a server, just make sure those migrations are run in that environment and you're good to go.

## Migrations class properties

### `\CI_DB_mysqli_forge $dbForge`

### `\EllisLab\ExpressionEngine\Service\Database\Query $queryBuilder`

### `\EllisLab\ExpressionEngine\Service\Model\Facade $modelFacade`

### `\BuzzingPixel\Executive\SchemaDesign\ChannelDesigner $channelDesigner`

### `\BuzzingPixel\Executive\SchemaDesign\LayoutDesigner $layoutDesigner`
