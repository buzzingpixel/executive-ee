<?php

declare(strict_types=1);

namespace sample\name\space;

/**
 * TODO: This dependency is only an example, and would only be used for commands
 * It would not be used for a standard service, or a web controller
 */

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Sample dependency injection set up in your config file. Remove this entire
 * comment block afterwards
```
$config['diDefinitions'] = [
    \sample\name\space\ClassTemplate::class => function () {
        return new \sample\name\space\ClassTemplate(
            new \Symfony\Component\Console\Output\ConsoleOutput()
        );
    }
];
```
 */

/**
 * Sample to add a command to config file. Remove this entire comment block
 * afterwards
```
$config['commands'] = [
    'customCommand' => [
        'class' => \sample\name\space\ClassTemplate::class,
        'customMethod' => 'customMethod',
        'description' => 'TODO: Describe command for display in console',
    ],
];
```
 */


class ClassTemplate
{
    /** @var OutputInterface $consoleOutput */
    private $consoleOutput;

    /**
     * @todo Set up all dependency injections
     * ClassTemplate constructor
     */
    public function __construct(OutputInterface $consoleOutput)
    {
        $this->consoleOutput = $consoleOutput;
    }

    /**
     * @todo Name method appropriately and write appropriate description here
     * Writes "Hello {$someArgument} World!" to the console
     * php ee executive user customCommand --someArgument=SomeValue
     */
    public function customMethod(string $someArgument) : void
    {
        // TODO: Update this method to do specific tasks
        $this->consoleOutput->writeln(
            "<fg=green>Hello {$someArgument} World!</>"
        );
    }
}
