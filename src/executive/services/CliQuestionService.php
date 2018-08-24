<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services;

use EE_Lang;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperInterface;
use buzzingpixel\executive\factories\ConsoleQuestionFactory;

/**
 * Class CliQuestionService
 */
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
     * @param HelperInterface $questionHelper
     * @param InputInterface $consoleInput
     * @param OutputInterface $consoleOutput
     * @param ConsoleQuestionFactory $consoleQuestionFactory
     * @param EE_Lang $lang
     */
    public function __construct(
        HelperInterface $questionHelper,
        InputInterface $consoleInput,
        OutputInterface $consoleOutput,
        ConsoleQuestionFactory $consoleQuestionFactory,
        EE_Lang $lang
    ) {
        $this->questionHelper = $questionHelper;
        $this->consoleInput = $consoleInput;
        $this->consoleOutput = $consoleOutput;
        $this->consoleQuestionFactory = $consoleQuestionFactory;
        $this->lang = $lang;
    }

    /**
     * Runs the installation
     * @param string $question
     * @param bool $required
     * @return string
     */
    public function ask(string $question, bool $required = true): string
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
                return \is_string($val) ? $val : '';
            }

            if (! $val) {
                $this->consoleOutput->writeln(
                    '<fg=red>' .
                    $this->lang->line('youMustProvideAValue') .
                    '</>'
                );
            }
        }

        return $val;
    }
}
