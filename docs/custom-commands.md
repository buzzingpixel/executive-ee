# Custom Commands

Custom commands per site in Executive are pretty cool. The quick explanation is that you create a class in the `system/user/*` directory that extends `\BuzzingPixel\Executive\Abstracts\BaseCommand`, give it a method, then define the command in your config file. It will then be available on the Executive command line.

Executive includes a command to create a skeleton of the command for you:

`php ee executive makeCommand --description=MyCommandDescription`

After you've written your command, simple add your command to the config file:

```php
$config['commands'] = array(
    'testCommand' => array(
        'class' => '\User\Command\TestCommand',
        'method' => 'testMethod',
        'description' => 'This is a test description',
    ),
);
```
