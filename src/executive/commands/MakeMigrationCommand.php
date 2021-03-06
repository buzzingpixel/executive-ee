<?php

declare(strict_types=1);

namespace buzzingpixel\executive\commands;

use buzzingpixel\executive\services\CaseConversionService;
use buzzingpixel\executive\services\CliQuestionService;
use buzzingpixel\executive\services\TemplateMakerService;
use DateTime;
use EE_Lang;
use Symfony\Component\Console\Output\OutputInterface;
use const DIRECTORY_SEPARATOR;
use function mb_strlen;
use function mb_strtolower;
use function range;
use function rtrim;

class MakeMigrationCommand
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
    /** @var string $migrationNameSpace */
    private $migrationNameSpace;
    /** @var string $migrationDestination */
    private $migrationDestination;

    /**
     * CacheCommand constructor
     */
    public function __construct(
        OutputInterface $consoleOutput,
        CliQuestionService $cliQuestionService,
        EE_Lang $lang,
        CaseConversionService $pascaleCaseService,
        TemplateMakerService $templateMakerService,
        string $templateLocation,
        string $migrationNameSpace,
        string $migrationDestination
    ) {
        $this->consoleOutput        = $consoleOutput;
        $this->cliQuestionService   = $cliQuestionService;
        $this->lang                 = $lang;
        $this->pascaleCaseService   = $pascaleCaseService;
        $this->templateMakerService = $templateMakerService;
        $this->templateLocation     = $templateLocation;
        $this->migrationNameSpace   = $migrationNameSpace;
        $this->migrationDestination = rtrim(
            rtrim($migrationDestination, '/'),
            DIRECTORY_SEPARATOR
        );
    }

    /**
     * Makes a command class
     */
    public function make(?string $name = null) : void
    {
        $hasBlockingErrors = false;

        if (! $this->migrationNameSpace) {
            $hasBlockingErrors = true;

            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('specifyMigrationNamespace') .
                '</>'
            );
        }

        if (! $this->migrationDestination) {
            $hasBlockingErrors = true;

            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('specifyMigrationDestination') .
                '</>'
            );
        }

        if ($hasBlockingErrors) {
            return;
        }

        if (! $this->templateLocation) {
            $this->templateLocation = EXECUTIVE_PATH . '/templates/Migration.php';

            $this->consoleOutput->writeln(
                '<fg=yellow>' .
                $this->lang->line('usingDefaultMigrationTemplate') .
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

        /** @noinspection PhpUnhandledExceptionInspection */
        $date = new DateTime();

        $namePrefix = 'm' . $date->format('Y_m_d_His');
        $first      = true;
        foreach (range(mb_strlen($namePrefix), 18) as $key => $val) {
            if ($first) {
                $first = false;
                continue;
            }
            $namePrefix .= '0';
        }

        $name = $namePrefix . '_' . $name;

        $destination = $this->migrationDestination . DIRECTORY_SEPARATOR .
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

        if (mb_strtolower($proceed) !== 'y') {
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('aborting') .
                '</>'
            );

            return;
        }

        $makeTemplateStatus = $this->templateMakerService->makeTemplate(
            'Migration',
            $name,
            $this->migrationNameSpace,
            $this->templateLocation,
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

            $this->consoleOutput->writeln('<fg=red>' . $lang . '</>');

            return;
        }

        $this->consoleOutput->writeln(
            '<fg=green>' .
            $this->lang->line('migrationCreated') .
            '</>'
        );
    }
}
