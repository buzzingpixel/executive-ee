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
use buzzingpixel\executive\exceptions\InvalidTemplateConfiguration;

/**
 * Class MakeFromTemplateCommand
 */
class MakeFromTemplateCommand
{
    /** @var OutputInterface $consoleOutput */
    private $consoleOutput;

    /** @var CliQuestionService $cliQuestionService */
    private $cliQuestionService;

    /** @var EE_Lang $lang */
    private $lang;

    /** @var CaseConversionService $caseConversionService */
    private $caseConversionService;

    /** @var TemplateMakerService $templateMakerService */
    private $templateMakerService;

    /** @var array $availableConfigurations */
    private $availableConfigurations;

    /**
     * CacheCommand constructor
     * @param OutputInterface $consoleOutput
     * @param CliQuestionService $cliQuestionService
     * @param EE_Lang $lang
     * @param CaseConversionService $caseConversionService
     * @param TemplateMakerService $templateMakerService
     * @param array $availableConfigurations
     */
    public function __construct(
        OutputInterface $consoleOutput,
        CliQuestionService $cliQuestionService,
        EE_Lang $lang,
        CaseConversionService $caseConversionService,
        TemplateMakerService $templateMakerService,
        array $availableConfigurations
    ) {
        $this->consoleOutput = $consoleOutput;
        $this->cliQuestionService = $cliQuestionService;
        $this->lang = $lang;
        $this->caseConversionService = $caseConversionService;
        $this->templateMakerService = $templateMakerService;
        $this->availableConfigurations = $availableConfigurations;
    }

    /**
     * Makes a command class
     * @param string $configuration
     * @param string $className
     * @throws InvalidTemplateConfiguration
     */
    public function make(
        ?string $configuration = null,
        ?string $className = null
    ): void {
        foreach ($this->availableConfigurations as $name => $config) {
            if (! $name) {
                throw new InvalidTemplateConfiguration(
                    $this->lang->line('invalidTemplateConfigurationName')
                );
            }

            if (! isset($config['namespace']) ||
                ! \is_string($config['namespace'])
            ) {
                throw new InvalidTemplateConfiguration(
                    str_replace(
                        '{{key}}',
                        '"' . $name . '"',
                        $this->lang->line('invalidTemplateConfigurationNameSpace')
                    )
                );
            }

            if (! isset($config['destination']) ||
                ! \is_string($config['destination'])
            ) {
                throw new InvalidTemplateConfiguration(
                    str_replace(
                        '{{key}}',
                        '"' . $name . '"',
                        $this->lang->line('invalidTemplateConfigurationDestination')
                    )
                );
            }
        }

        if (! $this->availableConfigurations) {
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('noClassTemplateConfigurationsAvailable') .
                '</>'
            );

            return;
        }

        $chosenConfig = $this->availableConfigurations[
            $configuration ?: $this->getConfigurationKey()
        ] ?? null;

        if (! $chosenConfig) {
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('invalidConfigOption') .
                '</>'
            );

            return;
        }

        if (! $className) {
            $className = $this->cliQuestionService->ask(
                '<fg=cyan>' .
                $this->lang->line('className') .
                ': </>'
            );
        }

        $className = $this->caseConversionService->convertStringToPascale(
            $className
        );

        $classNameSuffix = $chosenConfig['classNameSuffix'] ?? null;

        if ($classNameSuffix) {
            $len = \strlen($classNameSuffix);
            $rev = strrev($className);
            $suffixRev = strrev($classNameSuffix);

            if (stripos(strtolower($rev), strtolower($suffixRev)) === 0) {
                $className = strrev(substr($rev, $len));
            }

            $className .= $classNameSuffix;
        }

        $destination = $chosenConfig['destination'] . DIRECTORY_SEPARATOR .
            $className . '.php';

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

        $templateLocation = $chosenConfig['templateLocation'] ?? null;
        $classNameToReplace = $chosenConfig['classNameToReplace'] ?? null;

        if ($templateLocation && ! $classNameToReplace) {
            $this->consoleOutput->writeln(
                '<fg=yellow>' .
                $this->lang->line('usingDefaultClassNameToReplace') .
                '</>'
            );

            $classNameToReplace = 'ClassTemplate';
        }

        if (! $templateLocation) {
            $this->consoleOutput->writeln(
                '<fg=yellow>' .
                $this->lang->line('usingDefaultTemplate') .
                '</>'
            );

            $templateLocation = EXECUTIVE_PATH . '/templates/ClassTemplate.php';
            $classNameToReplace = 'ClassTemplate';
        }

        $makeTemplateStatus = $this->templateMakerService->makeTemplate(
            $classNameToReplace,
            $className,
            $chosenConfig['namespace'],
            $templateLocation,
            $destination
        );

        if ($makeTemplateStatus !==
            $this->templateMakerService::TEMPLATE_CREATED_SUCCESSFULLY
        ) {
            $lang = $this->lang->line($makeTemplateStatus);

            if ($lang === $makeTemplateStatus) {
                $this->consoleOutput->writeln(
                    '<fg=red>' .
                    $this->lang->line('unknownTemplateMakerError') .
                    '</>'
                );
                return;
            }

            $this->consoleOutput->writeln('<fg=red>' . $lang. '</>');

            return;
        }

        $this->consoleOutput->writeln(
            '<fg=green>' .
            $this->lang->line('classCreated') .
            '</>'
        );
    }

    /**
     * @return string
     */
    private function getConfigurationKey(): string
    {
        $this->consoleOutput->writeln(
            $this->lang->line('choseFromAvailableTemplateConfigs')
        );

        foreach (array_keys($this->availableConfigurations) as $config) {
            $this->consoleOutput->writeln(
                '<fg=green>  ' .
                $config .
                '</>'
            );
        }

        return $this->cliQuestionService->ask(
            '<fg=cyan>' .
            $this->lang->line('enterConfig') .
            ': </>'
        );
    }
}
