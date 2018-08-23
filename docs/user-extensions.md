# User Extensions

Executive provides the ability for extensions custom just to the application you're working on to run. Use a migration to install the extension and the `ExtensionDesignerService` to set the hook, class, and method of your extension.

```php
<?php
(new \buzzingpixel\executive\factories\ExtensionDesignerServiceFactory())->make()
    ->extClass(myapp\extensions\SampleExtension::class)
    ->extMethod('sampleMethod')
    ->extHook('before_channel_entry_update')
    ->extPriority(1) // Not required, defaults to 10
    ->add(); // You can also call the `remove()` method if you are removing an extension.
```

The method called will receive all arguments the given extension would normally send to the extension method.

When preparing the class, Executive will first try to get the class from the [Dependency Injector](dependency-injection.md). This way, you can have fully dependency injected and unit tested code. If you have not defined the class in the dependency injector config, Executive will fall back to trying to new up the class.
