<?php

declare(strict_types=1);

use buzzingpixel\executive\exceptions\DependencyInjectionBuilderException;
use buzzingpixel\executive\exceptions\InvalidViewConfigurationException;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\services\ViewService;
use EllisLab\ExpressionEngine\Core\Request as RequestService;
use EllisLab\ExpressionEngine\Library\CP\Table;
use EllisLab\ExpressionEngine\Service\Alert\AlertCollection;
use EllisLab\ExpressionEngine\Service\URL\URLFactory;

class Executive_mcp
{
    /** @var EE_Config $configService */
    private $configService;
    /** @var RequestService $requestService */
    private $requestService;
    /** @var URLFactory $urlFactory */
    private $urlFactory;
    /** @var ViewService $viewService */
    private $viewService;

    /**
     * Executive_mcp constructor
     *
     * @throws DependencyInjectionBuilderException
     */
    public function __construct()
    {
        $this->configService  = ee()->config;
        $this->requestService = ee('Request');
        $this->urlFactory     = ee('CP/URL');

        $this->viewService = ExecutiveDi::diContainer()->get(
            ViewService::INTERNAL_DI_NAME
        );
    }

    /**
     * Executive's control panel method
     *
     * @throws InvalidViewConfigurationException
     */
    public function index() : array
    {
        // Get controller param
        $section = $this->requestService->get('section');

        // If there is no section, show the index of available sections
        if (! $section) {
            return $this->showAvailableSections();
        }

        $sections = $this->configService->item('cpSections') ?: [];

        $sectionConfig = $sections[$section] ?? null;

        if (! $sectionConfig) {
            /** @var AlertCollection $eeAlertCollection */
            $eeAlertCollection = ee('CP/Alert');

            // Create the alert
            $eeAlertCollection->makeInline('shared-form')
                ->withTitle(lang('userCpSectionNotFound'))
                ->cannotClose()
                ->asIssue()
                ->now();

            return [
                'heading' => lang('userCpSectionNotFound'),
                'body' => $this->viewService->setView('CP/AlertOnly')
                    ->render(),
            ];
        }

        $pageKey = $this->requestService->get('page');

        $page = 'index';
        if ($pageKey) {
            $page = $pageKey;
        }

        try {
            $class = ExecutiveDi::diContainer()->get($sectionConfig[$page]['class']);
        } catch (Throwable $e) {
            $class = new $sectionConfig[$page]['class']();
        }

        return $class->{$sectionConfig[$page]['method']}();
    }

    /**
     * Shows available sections
     *
     * @return array
     *
     * @throws InvalidViewConfigurationException
     */
    private function showAvailableSections() : array
    {
        /** @var Table $table */
        $table = ee('CP/Table');

        $table->setColumns(['']);

        $table->setNoResultsText('noCpSections');

        $data = [];

        $sections = $this->configService->item('cpSections') ?: [];

        foreach ($sections as $sectionHandle => $section) {
            if (! isset($section['index']['title'])) {
                continue;
            }

            $data[][] = [
                'content' => $section['index']['title'],
                'href' => $this->urlFactory->make('addons/settings/executive', ['section' => $sectionHandle]),
            ];
        }

        $table->setData($data);

        return [
            'heading' => lang('userCpSections'),
            'body' => $this->viewService->setView('CP/Index')->render([
                'table' => $table->viewData(),
            ]),
        ];
    }
}
