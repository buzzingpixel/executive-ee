# Dependency Injection

Executive makes Dependency Injection in your custom classes for your project easy. And this enables you do easily do unit testing, and just generally use good design patterns.

Executive has a dependency injector it uses internally which it also exposes via the config file.

Here's an example:

```php
$config['diDefinitions'] = [
    \myapp\services\TestCpClass::class => function () {
        return new \myapp\services\TestCpClass(
            \buzzingpixel\executive\ExecutiveDi::get(
                \buzzingpixel\executive\services\ViewService::class
            ),
            ee('Model')
        );
    },
    \myapp\tags\TestTag::class => function () {
        return new \myapp\tags\TestTag(
            new \buzzingpixel\executive\factories\QueryBuilderFactory::class
        );
    },
];
```

Though in general it's good to try to avoid the "Service Locator" pattern, `\buzzingpixel\executive\ExecutiveDi::get()` and `\buzzingpixel\executive\ExecutiveDi::make()` are essentially service locators which you should mostly only use to get dependencies to inject into your classes. In other words, best practice is to use it only when defining your dependencies as above. All of Executive's own dependency injected classes are available through the DI `get()` or `make()` methods by the full class name (again, as seen above). Any dependencies you define here will also be available in the same manner by passing the key of the definition to the `get()` or `make()` methods on the DI.

Note the difference between `get()` and `make()`.

Internally `get()` will new up the class if this is the first time that class has been requested, and then cash it so any subsequent calls to `get()` for that class will return the same instance newed up by the first request for it. This is great for stateless services, or classes where configuration is costly and/or required to be the same at all times.

`make()` will always get a new instance of the class and never cache it or get it from cache.
