<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use buzzingpixel\executive\ExecutiveDi;
use EllisLab\ExpressionEngine\Library\CP\Table;
use EllisLab\ExpressionEngine\Service\URL\URLFactory;
use EllisLab\ExpressionEngine\Service\View\ViewFactory;
use EllisLab\ExpressionEngine\Service\Alert\AlertCollection;
use EllisLab\ExpressionEngine\Core\Request as RequestService;

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

    /** @var ViewFactory $viewFactory */
    private $viewFactory;

    /**
     * Executive_mcp constructor
     */
    public function __construct()
    {
        $this->configService = ee()->config;
        $this->requestService = ee('Request');
        $this->urlFactory = ee('CP/URL');
        $this->viewFactory = ee('View');
    }

    /**
     * Executive's control panel method
     * @return array
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

            return array(
                'heading' => lang('userCpSectionNotFound'),
                'body' => $this->viewFactory->make('executive:CP/AlertOnly')
                    ->render(),
            );
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
     */
    private function showAvailableSections(): array
    {
        /** @var Table $table */
        $table = ee('CP/Table');

        $table->setColumns(array(
            '',
        ));

        $table->setNoResultsText('noCpSections');

        $data = array();

        $sections = $this->configService->item('cpSections') ?: [];

        foreach ($sections as $sectionHandle => $section) {
            if (! isset($section['index']['title'])) {
                continue;
            }

            $data[][] = array(
                'content' => $section['index']['title'],
                'href' => $this->urlFactory
                    ->make('addons/settings/executive', array(
                        'section' => $sectionHandle
                    ))
            );
        }

        $table->setData($data);

        return array(
            'heading' => lang('userCpSections'),
            'body' => $this->viewFactory->make('executive:CP/Index')
                ->render(array(
                    'table' => $table->viewData(),
                ))
        );
    }
}
