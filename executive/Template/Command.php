<?php

namespace User\Command;

use BuzzingPixel\Executive\Abstracts\BaseCommand;

/**
 * Class Command
 */
class Command extends BaseCommand
{
    /**
     * // TODO: Name and implement first method
     * @param string $testArg
     * @param string $testArg2
     */
    public function firstMethod($testArg, $testArg2)
    {
        // TODO: remove these notes
        // Note: Arguments are received from the command line as follows:
        // php ee [group] [command] --testArg2=testValue2 --testArg=testValue
        // Note that order is not important, only the name
        print_r($testArg);
        print_r($testArg2);
    }
}
