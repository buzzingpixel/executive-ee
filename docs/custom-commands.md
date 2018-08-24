# Custom Commands

A custom command is a class method that can be run from the command line. Available commands are defined in the EE config file:

```php
$config['commands'] = [
    'testCommand' => [
        'class' => myapp\commands\SampleCommand::class,
        'method' => 'sampleMethod',
        'description' => 'This is a sample description',
    ],
];
```

When preparing the command to run, Executive will first try to get the command from the [Dependency Injector](dependency-injection.md). This way, you can have fully dependency injected and unit tested code. If you have not defined the class in the dependency injector config, Executive will fall back to trying to new up the class.
