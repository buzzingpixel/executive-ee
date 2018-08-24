# Addon Commands

Add-ons can have [cli commands](custom-commands.md) as well. To add a command available to the Executive command line from your add-on, simple provide the following in your add-on's addon.setup file.

```php
<?php
return array(
    'author' => 'Bill Smith',
    'description' => 'My Cool Addon does things',
    'name' => 'My Cool Addon',
    'namespace' => 'SmithAndCo\MyCoolAddon',
    'version' => '2.3.4',
    'commands' => array(
        'myCoolCommand' => array(
            'class' => \SmithAndCo\MyCoolAddon\Command\MyCoolCommand::class,
            'method' => 'run',
            'description' => lang('myCoolCommandDescription'),
        ),
        'anotherCommand' => array(
            'class' => 'my_cool_addon:AnotherCommandClass',
            'method' => 'run',
            'description' => lang('myCoolCommandDescription'),
        ),
    ),
);
```

Keen observers will note in the second example that a fully qualified class name is not used, but that it looks an awful lot like what you would pass to the `ee()` service locator. And that's exactly what it is. Executive will attempt to load the class from EE's service locator and falls back to newing up the class if EE's service locator is unsuccessful.
