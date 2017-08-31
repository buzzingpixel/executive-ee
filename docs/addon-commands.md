# Addon Commands

Add-ons can have custom commands as well. To add a command available to the Executive command line from your add-on, simple provide the following in your addon.setup file.

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
            'class' => '\SmithAndCo\MyCoolAddon\Command\MyCoolCommand',
            'method' => 'run',
            'description' => lang('myCoolCommandDescription'),
        ),
    ),
);
```
