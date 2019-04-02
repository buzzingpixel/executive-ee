# Migrations

One of the big power plays for Executive is Migrations. Migrations are classes with a single method that is run once when you `runMigrations`. Those of you who use Laravel or other applications with migrations (or Phinx) can probably skip the details, but for clarity's sake:

You create a migration file/class and in the `safeUp` method, you write code that manipulates your database. Executive keeps track of the migrations classes that have already run and runs any that haven't been run. Ideally you would have your deployment process set up to run the `runMigrations` command right after deploy. This allows you to make schema changes once, in code, and each environment gets the required schema changes when the code gets out to that environment.

## Configuration

Executive just needs a little bit of setup to know where to store the migration class files, and what namespace they should have.

### $config['migrationNamespace']

Example: `$config['migrationNamespace'] = 'MyApp\Migrations';`

### $config['migrationDestination']

Example: `$config['migrationDestination'] = APP_DIR . '/src/Migrations/';`

## Creating migrations

In your development environment, you would run the command:

`php ee executive makeMigration`

Executive will ask you to give the migration file a name.

Once created, you edit the resulting migration file that was created to do your migrations.

After you're done writing your migration class, in your development environment run:

`php ee executive runMigrations`

Your new migration will be run and your schema updated. You can then go on developing. When you're ready to deploy to a server, just make sure those migrations are run in that environment and you're good to go.

## MigrationAbstract

By default, when you create migrations, the resulting class will extend Executive's `MigrationAbstract` class which implements the correct `MigrationInterface` and provides access to some factory classes to help you design your migrations.

### `\buzzingpixel\executive\factories\DBForgeFactory $dbForgeFactory`

Use `$this->dbForgeFactory->make()` to get a `\CI_DB_mysqli_forge` instance for manipulating the database schema.

For reference, this gets the same thing `ee()->dbforge` gets.

### `\buzzingpixel\executive\factories\QueryBuilderFactory $queryBuilderFactory`

Use `$this->queryBuilderFactory->make()` to get a `\EllisLab\ExpressionEngine\Service\Database\Query` instance for querying the database.

For reference, this gets the same thing `ee('db')` (or in older EE, `ee()->db`) gets.

### `\buzzingpixel\executive\factories\ModelFacadeFactory $modelFacadeFactory`

Use `$this->modelFacadeFactory->make()` to get a `\EllisLab\ExpressionEngine\Service\Model\Facade` instance for getting or making ExpressionEngine models.

For reference, this gets the same thing `ee('Model')` gets.

### `\buzzingpixel\executive\factories\ChannelDesignerServiceFactory $channelDesignerFactory`

Use `$this->channelDesignerFactory->make()` to get a `\buzzingpixel\executive\services\ChannelDesignerService` instance.

### `\buzzingpixel\executive\factories\ExtensionDesignerServiceFactory $extensionDesignerFactory`

Use `$this->extensionDesignerFactory->make()` to get a `\buzzingpixel\executive\services\ExtensionDesignerService` instance.

### `\buzzingpixel\executive\factories\LayoutDesignerServiceFactory $layoutDesignerFactory`

Use `$this->layoutDesignerFactory->make()` to get a `\buzzingpixel\executive\services\LayoutDesignerService` instance.
