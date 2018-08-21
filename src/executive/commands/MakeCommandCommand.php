<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\commands;

use EE_Lang;
use Symfony\Component\Console\Output\OutputInterface;
use buzzingpixel\executive\services\CliQuestionService;
use buzzingpixel\executive\services\TemplateMakerService;
use buzzingpixel\executive\services\CaseConversionService;

/**
 * Class MakeCommandCommand
 */
class MakeCommandCommand
{
    /** @var OutputInterface $consoleOutput */
    private $consoleOutput;

    /** @var CliQuestionService $cliQuestionService */
    private $cliQuestionService;

    /** @var EE_Lang $lang */
    private $lang;

    /** @var CaseConversionService $pascaleCaseService */
    private $pascaleCaseService;

    /** @var TemplateMakerService $templateMakerService */
    private $templateMakerService;

    /** @var string $templateLocation */
    private $templateLocation;

    /** @var string $commandNameSpace */
    private $commandNameSpace;

    /** @var string $makeCommandDestination */
    private $makeCommandDestination;

    /**
     * CacheCommand constructor
     * @param OutputInterface $consoleOutput
     * @param CliQuestionService $cliQuestionService
     * @param EE_Lang $lang
     * @param CaseConversionService $pascaleCaseService
     * @param TemplateMakerService $templateMakerService
     * @param string $templateLocation
     * @param string $commandNameSpace
     * @param string $makeCommandDestination
     */
    public function __construct(
        OutputInterface $consoleOutput,
        CliQuestionService $cliQuestionService,
        EE_Lang $lang,
        CaseConversionService $pascaleCaseService,
        TemplateMakerService $templateMakerService,
        string $templateLocation,
        string $commandNameSpace,
        string $makeCommandDestination
    ) {
        $this->consoleOutput = $consoleOutput;
        $this->cliQuestionService = $cliQuestionService;
        $this->lang = $lang;
        $this->pascaleCaseService = $pascaleCaseService;
        $this->templateMakerService = $templateMakerService;
        $this->templateLocation = $templateLocation;
        $this->commandNameSpace = $commandNameSpace;
        $this->makeCommandDestination = $makeCommandDestination;
    }

    /**
     * Makes a command class
     * @param string $name
     */
    public function make(?string $name = null): void
    {
        $hasBlockingErrors = false;

        if (! $this->commandNameSpace) {
            $hasBlockingErrors = true;

            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('specifyMakeCommandNamespace') .
                '</>'
            );
        }

        if (! $this->makeCommandDestination) {
            $hasBlockingErrors = true;

            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('specifyMakeCommandDestination') .
                '</>'
            );
        }

        if ($hasBlockingErrors) {
            return;
        }

        if (! $this->templateLocation) {
            $this->templateLocation = EXECUTIVE_PATH . '/templates/Command.php';

            $this->consoleOutput->writeln(
                '<fg=yellow>' .
                $this->lang->line('usingDefaultCommandTemplate') .
                '</>'
            );
        }

        if (! $name) {
            $name = $this->cliQuestionService->ask(
                '<fg=cyan>' .
                $this->lang->line('className') .
                ': </>'
            );
        }

        $name = $this->pascaleCaseService->convertStringToPascale($name);
        $rev = strrev($name);

        if (stripos(strtolower($rev), 'dnammoc') === 0) {
            $name = strrev(substr($rev, 7));
        }

        $name .= 'Command';

        $destination = $this->makeCommandDestination . DIRECTORY_SEPARATOR .
            $name . '.php';

        $proceed = $this->cliQuestionService->ask(
            '<fg=cyan>' .
            $this->lang->line('createFileAt') .
            '</> ' .
            '<fg=green>' .
            $destination .
            '</>' .
            '<fg=cyan>' .
            '? (y/n): ' .
            '</>'
        );

        if (strtolower($proceed) !== 'y') {
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('aborting') .
                '</>'
            );
            return;
        }

        $makeTemplateStatus = $this->templateMakerService->makeTemplate(
            'Command',
            $name,
            $this->commandNameSpace,
            $this->templateLocation,
            $destination
        );

        if ($makeTemplateStatus ===
            $this->templateMakerService::DESTINATION_EXISTS_ERROR
        ) {
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('fileExistsAtDestination') .
                '</>'
            );
            return;
        }

        if ($makeTemplateStatus ===
            $this->templateMakerService::CANNOT_CREATE_DESTINATION_DIRECTORY_ERROR
        ) {
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('cannotCreateTemplateDirectory') .
                '</>'
            );
            return;
        }

        if ($makeTemplateStatus !==
            $this->templateMakerService::TEMPLATE_CREATED_SUCCESSFULLY
        ) {
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('unknownTemplateMakerError') .
                '</>'
            );
            return;
        }

        $this->consoleOutput->writeln(
            '<fg=green>' .
            $this->lang->line('commandCreated') .
            '</>'
        );
    }
}
