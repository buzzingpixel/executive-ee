# User Extensions

Executive provides the ability for extensions custom just to the application you're working on to run. Use a migration to install the extension and the `ExtensionDesigner` to set the hook, class, and method of your extension.

```php
<?php
(new \BuzzingPixel\Executive\SchemaDesign\ExtensionDesigner())
    ->extClass('\User\Extension\TestExtension')
    ->extMethod('testMethod')
    ->extHook('before_channel_entry_update')
    ->extPriority(1) // Not required, defaults to 10
    ->add(); // You can also call the `remove()` method if you are removing an extension.
```

The method called will receive all arguments the given extension would normally send to the extension method.

Remember that Executive will auto-load any classes in the `user` directory as long as the namespace for the class starts with `User` and the rest of the namespace follows directory convention.
