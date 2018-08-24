# Addon Schedule

Add-ons can schedule commands to run at particular intervals by adding the schedule to the add-on's addon.setup file.

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
    ),
    'schedule' => array(
        array(
            'group' => 'my_cool_addon',
            'command' => 'myCoolCommand',
            'runEvery' => 'Week', // Always|FiveMinutes|TenMinutes|ThirtyMinutes|Hour|Day|Week|Month - or specify an integer of minutes
            'arguments' => array(
                'foo' => 'bar',
                'foobar' => 'barfoo',
            ),
        ),
    ),
);
```
