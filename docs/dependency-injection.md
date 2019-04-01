# Dependency Injection

Executive makes Dependency Injection in your custom classes for your project easy. And this enables you do easily do unit testing, and just generally use good design patterns.

Executive has a dependency injector it uses ([PHP-DI]([http://php-di.org/)) internally, and which it also exposes via the config file.

Here's an example:

```php
<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;

$config['diDefinitions'] = [
    \myapp\services\TestCpClass::class => function (ContainerInterface $di) {
        return new \myapp\services\TestCpClass(
            $di->get(\buzzingpixel\executive\services\ViewService::class),
            ee('Model')
        );
    },
    \myapp\tags\TestTag::class => function () {
        return new \myapp\tags\TestTag(
            new \buzzingpixel\executive\factories\QueryBuilderFactory()
        );
    },
];
```

Though in general it's good to try to avoid the "Service Locator" pattern, `\buzzingpixel\executive\ExecutiveDi::diContainer()` will get you the PHP-DI container instance which you can use essentially as a service locator when you really need to. But mostly you should define your dependencies as above. And the DI will auto-wire and use annotations wherever possible. In general you only need to define dependencies that auto-wiring can't figure out.
