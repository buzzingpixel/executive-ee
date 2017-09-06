# Schedule

Executive provides the ability to schedule commands to run at certain intervals. In order for this to work you must have a cron running the schedule command every minute.

Here is an example of the cron:

```shell
* * * * * /user/bin/php /path/to/ee executive runSchedule >> /dev/null 2>&1
```

## Scheduling commands

In your config file, provide an array of commands to run as follows:

```php
$config['schedule'] = array(
    array(
        'group' => 'user',
        'command' => 'testUserCommand',
        'runEvery' => 'FiveMinutes', // Always|FiveMinutes|TenMinutes|ThirtyMinutes|Hour|Day|Week|Month|DayAtMidnight|SaturdayAtMidnight|SundayAtMidnight|MondayAtMidnight|TuesdayAtMidnight|WednesdayAtMidnight|ThursdayAtMidNight|FridayAtMidnight
        'arguments' => array(
            'foo' => 'bar',
            'foobar' => 'barfoo',
        ),
    ),
    array(
        'group' => 'user',
        'command' => 'testUserCommand2',
        'runEvery' => 'Day', // Always|FiveMinutes|TenMinutes|ThirtyMinutes|Hour|Day|Week|Month|DayAtMidnight|SaturdayAtMidnight|SundayAtMidnight|MondayAtMidnight|TuesdayAtMidnight|WednesdayAtMidnight|ThursdayAtMidNight|FridayAtMidnight
    ),
    array(
        'group' => 'user',
        'command' => 'testUserCommand2',
        'runEvery' => 1440, // You can also specify minutes here
    ),
);
```
