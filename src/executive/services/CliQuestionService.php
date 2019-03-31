<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services;

use buzzingpixel\executive\factories\ConsoleQuestionFactory;
use EE_Lang;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function is_string;

class CliQuestionService
{
    /** @var QuestionHelper $questionHelper */
    private $questionHelper;
    /** @var InputInterface $consoleInput */
    private $consoleInput;
    /** @var OutputInterface $consoleOutput */
    private $consoleOutput;
    /** @var ConsoleQuestionFactory $consoleQuestionFactory */
    private $consoleQuestionFactory;
    /** @var EE_Lang $lang */
    private $lang;

    /**
     * CliInstallService constructor
     */
    public function __construct(
        HelperInterface $questionHelper,
        InputInterface $consoleInput,
        OutputInterface $consoleOutput,
        ConsoleQuestionFactory $consoleQuestionFactory,
        EE_Lang $lang
    ) {
        $this->questionHelper         = $questionHelper;
        $this->consoleInput           = $consoleInput;
        $this->consoleOutput          = $consoleOutput;
        $this->consoleQuestionFactory = $consoleQuestionFactory;
        $this->lang                   = $lang;
    }

    /**
     * Runs the installation
     */
    public function ask(string $question, bool $required = true) : string
    {
        $questionEntity = $this->consoleQuestionFactory->make($question);

        $val = '';

        while (! $val) {
            $val = $this->questionHelper->ask(
                $this->consoleInput,
                $this->consoleOutput,
                $questionEntity
            );

            if (! $required) {
                return is_string($val) ? $val : '';
            }

            if ($val) {
                continue;
            }

            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('youMustProvideAValue') .
                '</>'
            );
        }

        return $val;
    }
}
