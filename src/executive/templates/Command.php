<?php

namespace sample\name\space;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Sample to add command to config file. Remove this entire comment block
 * afterwards
```
$config['commands'] = [
    'customCommand' => [
        'class' => \sample\name\space\Command::class,
        'customMethod' => 'customMethod',
        'description' => 'TODO: Describe command for display in console',
    ],
];
```
 */

/**
 * Sample dependency injection set up in your config file. Remove this entire
 * comment block afterwards
```
$config['diDefinitions'] = [
    \sample\name\space\Command::class => function () {
        return new \sample\name\space\Command(
            new \Symfony\Component\Console\Output\ConsoleOutput()
        );
    }
];
```
 */

/**
 * Class Command
 */
class Command
{
    /** @var OutputInterface $consoleOutput */
    private $consoleOutput;

    /**
     * @todo Set up all dependency injections
     * Command constructor
     * @param OutputInterface $consoleOutput
     */
    public function __construct(OutputInterface $consoleOutput)
    {
        $this->consoleOutput = $consoleOutput;
    }

    /**
     * @todo Name method appropriately and write appropriate description here
     * Writes "Hello {$someArgument} World!" to the console
     * php ee executive user customCommand --someArgument=SomeValue
     * @param string $someArgument
     */
    public function customMethod($someArgument): void
    {
        // TODO: Update this method to do specific tasks
        $this->consoleOutput->writeln(
            "<fg=green>Hello {$someArgument} World!</>"
        );
    }
}
