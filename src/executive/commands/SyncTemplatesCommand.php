<?php

declare(strict_types=1);

namespace buzzingpixel\executive\commands;

use buzzingpixel\executive\services\templatesync\DeleteSnippetsNotOnDiskService;
use buzzingpixel\executive\services\templatesync\DeleteTemplateGroupsWithoutTemplatesService;
use buzzingpixel\executive\services\templatesync\DeleteTemplatesNotOnDiskService;
use buzzingpixel\executive\services\templatesync\DeleteVariablesNotOnDiskService;
use buzzingpixel\executive\services\templatesync\EnsureIndexTemplatesExistService;
use buzzingpixel\executive\services\templatesync\ForceSnippetVarSyncToDatabaseService;
use buzzingpixel\executive\services\templatesync\SyncTemplatesFromFilesService;
use EE_Config;
use EE_Lang;
use LogicException;
use Symfony\Component\Console\Output\OutputInterface;

class SyncTemplatesCommand
{
    /** @var EE_Lang $lang */
    private $lang;
    /** @var OutputInterface $output */
    private $output;
    /** @var EE_Config $config */
    private $config;
    /** @var DeleteVariablesNotOnDiskService $deleteVariablesNotOnDisk */
    private $deleteVariablesNotOnDisk;
    /** @var EnsureIndexTemplatesExistService $ensureIndexTemplatesExist */
    private $ensureIndexTemplatesExist;
    /** @var DeleteSnippetsNotOnDiskService $deleteSnippetsNotOnDisk */
    private $deleteSnippetsNotOnDisk;
    /** @var DeleteTemplatesNotOnDiskService $deleteTemplatesNotOnDisk */
    private $deleteTemplatesNotOnDisk;
    /** @var ForceSnippetVarSyncToDatabaseService $forceSnippetVarSyncToDatabase */
    private $forceSnippetVarSyncToDatabase;
    /** @var SyncTemplatesFromFilesService $syncTemplatesFromFiles */
    private $syncTemplatesFromFiles;
    /** @var DeleteTemplateGroupsWithoutTemplatesService $deleteTemplateGroupsWithoutTemplates */
    private $deleteTemplateGroupsWithoutTemplates;

    public function __construct(
        EE_Lang $lang,
        OutputInterface $output,
        EE_Config $config,
        DeleteVariablesNotOnDiskService $deleteVariablesNotOnDisk,
        EnsureIndexTemplatesExistService $ensureIndexTemplatesExist,
        DeleteSnippetsNotOnDiskService $deleteSnippetsNotOnDisk,
        DeleteTemplatesNotOnDiskService $deleteTemplatesNotOnDisk,
        ForceSnippetVarSyncToDatabaseService $forceSnippetVarSyncToDatabase,
        SyncTemplatesFromFilesService $syncTemplatesFromFiles,
        DeleteTemplateGroupsWithoutTemplatesService $deleteTemplateGroupsWithoutTemplates
    ) {
        $this->lang                                 = $lang;
        $this->output                               = $output;
        $this->config                               = $config;
        $this->deleteVariablesNotOnDisk             = $deleteVariablesNotOnDisk;
        $this->ensureIndexTemplatesExist            = $ensureIndexTemplatesExist;
        $this->deleteSnippetsNotOnDisk              = $deleteSnippetsNotOnDisk;
        $this->deleteTemplatesNotOnDisk             = $deleteTemplatesNotOnDisk;
        $this->forceSnippetVarSyncToDatabase        = $forceSnippetVarSyncToDatabase;
        $this->syncTemplatesFromFiles               = $syncTemplatesFromFiles;
        $this->deleteTemplateGroupsWithoutTemplates = $deleteTemplateGroupsWithoutTemplates;
    }

    public function run() : void
    {
        if ($this->config->item('save_tmpl_files') !== 'y') {
            throw new LogicException(
                $this->lang->line('saveTmplFilesDisabled')
            );
        }

        $this->output->writeln(
            '<comment>' . $this->lang->line('syncingTemplates') . '</comment>'
        );

        $this->config->set_item('save_tmpl_files', 'n');

        $this->output->writeln(
            '<comment>' . $this->lang->line('ensuringIndexTemplatesExist') . '</comment>'
        );

        $this->ensureIndexTemplatesExist->run();

        $this->output->writeln(
            '<comment>' . $this->lang->line('deletingVariablesNotOnDisk') . '</comment>'
        );

        $this->deleteVariablesNotOnDisk->run();

        $this->output->writeln(
            '<comment>' . $this->lang->line('deletingSnippetsNotOnDisk') . '</comment>'
        );

        $this->deleteSnippetsNotOnDisk->run();

        $this->output->writeln(
            '<comment>' . $this->lang->line('deletingTemplatesNotOnDisk') . '</comment>'
        );

        $this->deleteTemplatesNotOnDisk->run();

        $this->config->set_item('save_tmpl_files', 'y');

        $this->output->writeln(
            '<comment>' . $this->lang->line('ensuringSnippetVarsSync') . '</comment>'
        );

        $this->forceSnippetVarSyncToDatabase->run();

        $this->output->writeln(
            '<comment>' . $this->lang->line('ensuringTemplatesSync') . '</comment>'
        );

        $this->syncTemplatesFromFiles->run();

        $this->deleteTemplateGroupsWithoutTemplates->run();

        $this->output->writeln(
            '<info>' . $this->lang->line('templateSyncComplete') . '</info>'
        );
    }
}
