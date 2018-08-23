<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use DI\NotFoundException;
use DI\DependencyException;
use buzzingpixel\executive\ExecutiveDi;
use EllisLab\ExpressionEngine\Library\CP\Table;
use buzzingpixel\executive\services\ViewService;
use EllisLab\ExpressionEngine\Service\URL\URLFactory;
use EllisLab\ExpressionEngine\Service\Alert\AlertCollection;
use EllisLab\ExpressionEngine\Core\Request as RequestService;
use buzzingpixel\executive\exceptions\InvalidViewConfigurationException;
use buzzingpixel\executive\exceptions\DependencyInjectionBuilderException;

/**
 * Class Executive_mcp
 */
// @codingStandardsIgnoreStart
class Executive_mcp
// @codingStandardsIgnoreEnd
{
    /** @var \EE_Config $configService */
    private $configService;

    /** @var RequestService $requestService */
    private $requestService;

    /** @var URLFactory $urlFactory */
    private $urlFactory;

    /** @var ViewService $viewService */
    private $viewService;

    /**
     * Executive_mcp constructor
     * @throws NotFoundException
     * @throws DependencyException
     * @throws DependencyInjectionBuilderException
     */
    public function __construct()
    {
        $this->configService = ee()->config;
        $this->requestService = ee('Request');
        $this->urlFactory = ee('CP/URL');

        $this->viewService = ExecutiveDi::get(
            ViewService::INTERNAL_DI_NAME
        );
    }

    /**
     * Executive's control panel method
     * @return array
     * @throws InvalidViewConfigurationException
     */
    public function index(): array
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
            $class = ExecutiveDi::get($sectionConfig[$page]['class']);
        } catch (\Throwable $e) {
            $class = new $sectionConfig[$page]['class'];
        }

        return $class->{$sectionConfig[$page]['method']}();
    }

    /**
     * Shows available sections
     * @return array
     * @throws InvalidViewConfigurationException
     */
    private function showAvailableSections(): array
    {
        /** @var Table $table */
        $table = ee('CP/Table');

        $table->setColumns([
            '',
        ]);

        $table->setNoResultsText('noCpSections');

        $data = [];

        $sections = $this->configService->item('cpSections') ?: [];

        foreach ($sections as $sectionHandle => $section) {
            if (! isset($section['index']['title'])) {
                continue;
            }

            $data[][] = [
                'content' => $section['index']['title'],
                'href' => $this->urlFactory->make('addons/settings/executive', [
                    'section' => $sectionHandle
                ])
            ];
        }

        $table->setData($data);

        return [
            'heading' => lang('userCpSections'),
            'body' => $this->viewService->setView('CP/Index')->render([
                'table' => $table->viewData(),
            ])
        ];
    }
}
