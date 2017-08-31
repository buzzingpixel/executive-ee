# Addon Schedule

Add-ons can schedule commands to run at particular intervals. You add the schedule to your addon.setup file.

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
