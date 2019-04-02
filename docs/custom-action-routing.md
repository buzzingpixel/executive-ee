# Custom Action Routing

Executive offers the ability to do custom routing for what are known as "action" requests.

Here's the basic gist of it: If a query parameter of `action` or a post body param of `action` is present in the request, Executive will attempt to match the request to an action defined in the config file.

## Config File Example

```php

$config['actions'] = [
    // Method __invoke is assumed if no method provided
    'testAction' => \MyApp\Actions\TestActionClass::class,
    'anotherAction' => [
        'class' => \MyApp\Actions\TestActionClass::class,
    ],
    'fooBar' => [
        'class' => \MyApp\Actions\TestActionClass::class,
        'method' => 'myMethod',
    ],
];
```

When preparing the action to run, Executive will first try to get the action class from the [Dependency Injector](dependency-injection.md). This way, you can have fully dependency injected and unit tested code. If for some reason the class can't be retrieved from the DI, Executive will fall back to trying to new up the class.
