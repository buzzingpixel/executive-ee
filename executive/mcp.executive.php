<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive;

use EllisLab\ExpressionEngine\Library\CP\Table;
use EllisLab\ExpressionEngine\Service\URL\URLFactory;
use EllisLab\ExpressionEngine\Core\Request as RequestService;
use EllisLab\ExpressionEngine\Service\View\ViewFactory;

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
     * Executive index page
     */
    public function index()
    {
        // Get controller param
        $section = $this->requestService->get('section');

        // If there is no section, show the index of available sections
        if (! $section) {
            return $this->showAvailableSections();
        }

        $sections = $this->configService->item('cpSections') ?: array();

        $sectionConfig = $sections[$section];

        $pageKey = $this->requestService->get('page');

        $page = 'index';
        if ($pageKey) {
            $page = $pageKey;
        }

        $class = new $sectionConfig[$page]['class'];

        return $class->{$sectionConfig[$page]['method']}();
    }

    /**
     * Show available sections
     */
    public function showAvailableSections()
    {
        /** @var Table $table */
        $table = ee('CP/Table');

        $table->setColumns(array(
            '',
        ));

        $table->setNoResultsText('noCpSections');

        $data = array();

        $sections = $this->configService->item('cpSections') ?: array();

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
