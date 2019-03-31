<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services;

use EllisLab\ExpressionEngine\Model\Channel\Channel as ChannelModel;
use EllisLab\ExpressionEngine\Model\Channel\ChannelField as ChannelFieldModel;
use EllisLab\ExpressionEngine\Model\Channel\ChannelLayout;
use EllisLab\ExpressionEngine\Model\Channel\Display\DefaultChannelLayout;
use EllisLab\ExpressionEngine\Model\Member\MemberGroup;
use EllisLab\ExpressionEngine\Model\Site\Site as SiteModel;
use EllisLab\ExpressionEngine\Service\Model\Collection as ModelCollection;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use Exception;
use function array_values;
use function in_array;
use function mb_strtolower;
use function str_replace;

class LayoutDesignerService
{
    /** @var ModelFacade $modelFacade */
    private $modelFacade;

    /** @var array $fieldNameToIdMap */
    private $fieldNameToIdMap = [];

    /** @var array $fieldIdStrToNameMap */
    private $fieldIdStrToNameMap = [];

    /** @var array $requiredFieldMap */
    private $requiredFieldMap = [
        'title' => true,
        'url_title' => true,
        'entry_date' => true,
    ];

    public function __construct(ModelFacade $modelFacade)
    {
        $this->modelFacade = $modelFacade;

        $fields = $this->modelFacade->get('ChannelField')->all();

        foreach ($fields as $field) {
            /** @var ChannelFieldModel $field */

            $fieldId    = $field->getProperty('field_id');
            $fieldIdStr = 'field_id_' . $fieldId;
            $fieldName  = $field->getProperty('field_name');

            $this->fieldNameToIdMap[$fieldName]     = $fieldId;
            $this->fieldIdStrToNameMap[$fieldIdStr] = $fieldIdStr;
            $this->requiredFieldMap[$fieldIdStr]    = $field->getProperty(
                'field_required'
            );
            $this->requiredFieldMap['title']        = true;
            $this->requiredFieldMap['url_title']    = true;
            $this->requiredFieldMap['entry_date']   = true;
        }
    }

    /** @var string $siteName */
    private $siteName = 'default_site';

    /**
     * Set the site name
     *
     * @return LayoutDesignerService
     */
    public function siteName(string $str) : self
    {
        $this->siteName = $str;

        return $this;
    }

    /** @var string $channel */
    private $channel;

    /**
     * Set the channel
     *
     * @return LayoutDesignerService
     */
    public function channel(string $str) : self
    {
        $this->channel = $str;

        return $this;
    }

    /** @var string $layoutName */
    private $layoutName;

    /**
     * Set the layout name
     *
     * @return LayoutDesignerService
     */
    public function layoutName(string $str) : self
    {
        $this->layoutName = $str;

        return $this;
    }

    /** @var array $memberGroups */
    private $addMemberGroups = [];

    /**
     * Add member group
     *
     * @return LayoutDesignerService
     */
    public function addMemberGroup(string $str) : self
    {
        $this->addMemberGroups[] = $str;

        return $this;
    }

    /** @var array $memberGroups */
    private $removeMemberGroups = [];

    /**
     * Remove member group
     *
     * @return LayoutDesignerService
     */
    public function removeMemberGroup(string $str) : self
    {
        $this->removeMemberGroups[] = $str;

        return $this;
    }

    /** @var string $tab */
    private $tab = 'Publish';

    /**
     * Set the tab to add fields to
     *
     * @return LayoutDesignerService
     */
    public function tab(string $str) : self
    {
        $this->tab = $str;

        return $this;
    }

    /** @var array $removeTabs */
    private $removeTabs = [];

    /**
     * Remove a tab
     *
     * @return LayoutDesignerService
     */
    public function removeTab(string $str) : self
    {
        $this->removeTabs[] = $str;

        return $this;
    }

    /** @var array $tabVisibility */
    private $tabVisibility = [];

    /**
     * Set whether the current working tab is visible
     *
     * @return LayoutDesignerService
     */
    public function tabIsVisible(bool $val = true) : self
    {
        $this->tabVisibility[$this->tab] = $val;

        return $this;
    }

    /** @var array $tabFields */
    private $tabFields = [];

    /** @var string $currentField */
    private $currentField;

    /**
     * Add field to current tab
     *
     * @return LayoutDesignerService
     */
    public function addField(string $str) : self
    {
        $this->currentField            = $str;
        $this->tabFields[$this->tab][] = $str;

        return $this;
    }

    /** @var array $fieldVisibility */
    private $fieldVisibility = [];

    /**
     * Set whether the current field is visible
     *
     * @return LayoutDesignerService
     */
    public function fieldIsVisible(bool $val = true) : self
    {
        $this->fieldVisibility[$this->currentField] = $val;

        return $this;
    }

    /** @var array $fieldCollapsed */
    private $fieldCollapsed = [];

    /**
     * Set whether the current field is collapsed
     *
     * @return LayoutDesignerService
     */
    public function fieldIsCollapsed(bool $val = true) : self
    {
        $this->fieldCollapsed[$this->currentField] = $val;

        return $this;
    }

    /**
     * Save schema design
     *
     * @throws Exception
     */
    public function save() : void
    {
        // Set required items
        $this->setSiteModel();
        $this->setChannelModel();
        $this->setLayoutModel();
        $this->processTabsAndFields();
        $this->processMemberGroups();
        $this->layoutModel->save();
    }

    /** @var SiteModel $siteModel */
    private $siteModel;

    /**
     * Set site model
     *
     * @throws Exception
     */
    private function setSiteModel() : void
    {
        if (! $this->siteName) {
            throw new Exception('Site name not defined');
        }

        /** @var SiteModel $site */
        $this->siteModel = $this->modelFacade->get('Site')
            ->filter('site_name', $this->siteName)
            ->first();

        if (! $this->siteModel) {
            throw new Exception('Site not found');
        }
    }

    /** @var ChannelModel $siteModel */
    private $channelModel;

    /**
     * Set Channel Model
     *
     * @throws Exception
     */
    private function setChannelModel() : void
    {
        if (! $this->channel) {
            throw new Exception('Channel not defined');
        }

        /** @var SiteModel $site */
        $this->channelModel = $this->modelFacade->get('Channel')
            ->filter('site_id', $this->siteModel->getProperty('site_id'))
            ->filter('channel_name', $this->channel)
            ->first();

        if (! $this->channelModel) {
            throw new Exception('Channel not found');
        }
    }

    /** @var ChannelLayout $layoutModel */
    private $layoutModel;

    /**
     * Set layout model
     *
     * @throws Exception
     */
    private function setLayoutModel() : void
    {
        if (! $this->layoutName) {
            throw new Exception('Layout name not defined');
        }

        $siteId    = $this->siteModel->getProperty('site_id');
        $channelId = $this->channelModel->getProperty('channel_id');

        /** @var ChannelLayout $layoutModel */
        $layoutModel = $this->modelFacade->get('ChannelLayout')
            ->filter('site_id', $siteId)
            ->filter('channel_id', $channelId)
            ->filter('layout_name', $this->layoutName)
            ->first();

        if (! $layoutModel) {
            $layoutModel = $this->modelFacade->make('ChannelLayout');
            $layoutModel->setProperty('site_id', $siteId);
            $layoutModel->setProperty('channel_id', $channelId);
            $layoutModel->setProperty('layout_name', $this->layoutName);
            $layoutModel->MemberGroups = null;
        }

        $this->layoutModel = $layoutModel;
    }

    /**
     * Process tabs and fields
     */
    private function processTabsAndFields() : void
    {
        // Start variables
        $placedFieldIds = [];
        $firstTabId     = 'publish';

        // The publish tab is always first and always visible
        $tabs = [
            'publish' => [
                'id' => 'publish',
                'name' => 'publish',
                'visible' => true,
                'fields' => [],
            ],
        ];

        // Iterate through user set tab fields
        foreach ($this->tabFields as $tab => $fieldNames) {
            /** @var array $fieldNames */

            // Create the tab ID
            $tabId = str_replace(' ', '_', mb_strtolower($tab));

            // If the tab ID is publish, it cannot be hidden
            if ($tabId === 'publish') {
                $this->tabVisibility[$tab] = true;
            }

            // Create the tab
            $tabs[$tabId] = [
                'id' => $tabId,
                'name' => $tab,
                'visible' => isset($this->tabVisibility[$tab]) ?
                    $this->tabVisibility[$tab] !== false :
                    true,
                'fields' => [],
            ];

            // Iterate through the field names
            foreach ($fieldNames as $fieldName) {
                // Set default field string
                $fieldIdStr = $fieldName;

                // Get the field ID
                $fieldId = $this->fieldNameToIdMap[$fieldName] ?? null;

                // Set the field string
                if ($fieldId) {
                    $fieldIdStr = "field_id_{$fieldId}";
                }

                // If the field is required, make sure it is visible
                if (isset($this->requiredFieldMap[$fieldIdStr]) &&
                    $this->requiredFieldMap[$fieldIdStr]
                ) {
                    $this->fieldVisibility[$fieldName] = true;
                }

                // Add the field to the fields array on the tab
                $tabs[$tabId]['fields'][] = [
                    'field' => $fieldIdStr,
                    'visible' => isset($this->fieldVisibility[$fieldName]) ?
                        $this->fieldVisibility[$fieldName] !== false :
                        true,
                    'collapsed' => isset($this->fieldCollapsed[$fieldName]) ?
                        $this->fieldCollapsed[$fieldName] === true :
                        false,
                ];

                // Make note that we've placed this field
                $placedFieldIds[$fieldIdStr] = $fieldIdStr;
            }
        }

        // Get the old tab layout
        /** @var array $oldLayout */
        $oldLayout = $this->layoutModel->getProperty('field_layout');

        // Iterate through the old layout
        foreach ($oldLayout as $layoutTab) {
            // Set the tab ID
            $tabId = $layoutTab['id'];

            // If we're supposed to remove this tab, let's stop
            if (in_array($layoutTab['name'], $this->removeTabs, true)) {
                continue;
            }

            // If we've already specified this tab above, move to next
            if (isset($tabs[$tabId])) {
                continue;
            }

            // Set up the tab
            $tabs[$tabId] = [
                'id' => $tabId,
                'name' => $layoutTab['name'],
                'visible' => isset($this->tabVisibility[$layoutTab['name']]) ?
                    $this->tabVisibility[$layoutTab['name']] !== false :
                    $layoutTab['visible'],
                'fields' => [],
            ];
        }

        // Iterate through the old tabs again and set fields
        foreach ($oldLayout as $layoutTab) {
            // Set the tab ID
            $tabId = $layoutTab['id'];

            // Add fields to the first tab if the tab is not set
            if (! isset($tabs[$tabId])) {
                $tabId = $firstTabId;
            }

            // Set the fields
            /** @var array $fields */
            $fields = $layoutTab['fields'];

            // Iterate through the fields
            foreach ($fields as $field) {
                // If the field has already been placed, skip it
                if (isset($placedFieldIds[$field['field']])) {
                    continue;
                }

                // Get the field string
                $fieldIdStr = $field['field'];

                // Get the field name
                $fieldName = $this->fieldIdStrToNameMap[$fieldIdStr] ?? $fieldIdStr;

                // If the field is required, make sure it is visible
                if (isset($this->requiredFieldMap[$fieldIdStr]) &&
                    $this->requiredFieldMap[$fieldIdStr]
                ) {
                    $this->fieldVisibility[$fieldName] = true;
                }

                // Add the field to the fields array on the tab
                $tabs[$tabId]['fields'][] = [
                    'field' => $fieldIdStr,
                    'visible' => isset($this->fieldVisibility[$fieldName]) ?
                        $this->fieldVisibility[$fieldName] !== false :
                        $field['visible'],
                    'collapsed' => isset($this->fieldCollapsed[$fieldName]) ?
                        $this->fieldCollapsed[$fieldName] === true :
                        $field['collapsed'],
                ];

                $placedFieldIds[$fieldIdStr] = $fieldIdStr;
            }
        }

        // Go through this channel's fields and make sure they have been set
        /** @var ModelCollection $channelFields */
        $channelFields = $this->channelModel->getAllCustomFields();
        foreach ($channelFields as $field) {
            /** @var ChannelFieldModel $field */
            $fieldIdStr = "field_id_{$field->getProperty('field_id')}";

            // If the field is set, we have nothing to do here
            if (isset($placedFieldIds[$fieldIdStr])) {
                continue;
            }

            // Add the field to the fields array on the tab
            $tabs[$firstTabId]['fields'][] = [
                'field' => $fieldIdStr,
                'visible' => false,
                'collapsed' => false,
            ];
        }

        // Get the default layout
        $defaultLayout = new DefaultChannelLayout(
            $this->channelModel->getProperty('channel_id'),
            null
        );
        $defaultLayout = $defaultLayout->getLayout();

        // Go through default layout and make sure all the default tabs are set
        foreach ($defaultLayout as $layoutTab) {
            // Set the tab ID
            $tabId = $layoutTab['id'];

            // Make sure the tab exists
            if (! isset($tabs[$tabId])) {
                $tabs[$tabId] = [
                    'id' => $layoutTab['id'],
                    'name' => $layoutTab['name'],
                    'visible' => false,
                    'fields' => [],
                ];
            }

            /** @var array $tabFields */
            $tabFields = $layoutTab['fields'];

            // Go through tab fields and make sure they're in the layout
            foreach ($tabFields as $tabField) {
                // Get the field ID string
                $fieldIdStr = $tabField['field'];

                // If the field is already in the layout
                if (isset($placedFieldIds[$fieldIdStr])) {
                    continue;
                }

                $tabs[$tabId]['fields'][] = $tabField;
            }
        }

        // Set the tabs on the layout model
        $this->layoutModel->setProperty('field_layout', array_values($tabs));
    }

    /**
     * Process remove member groups
     */
    private function processMemberGroups() : void
    {
        // Get the member groups
        /** @var ModelCollection $memberGroups */
        $memberGroups = $this->layoutModel->MemberGroups;

        $memberGroupIds = [];

        // Go through member groups and check if we should delete any of them
        foreach ($memberGroups as $memberGroup) {
            /** @var MemberGroup $memberGroup */

            if (in_array(
                $memberGroup->getProperty('group_title'),
                $this->removeMemberGroups,
                true
            )) {
                continue;
            }

            $id = $memberGroup->getProperty('group_id');

            $memberGroupIds[$id] = $id;
        }

        // If there are no member groups to add we can stop here
        if (! $this->addMemberGroups) {
            if (! $memberGroupIds) {
                $this->layoutModel->MemberGroups = null;

                return;
            }
            $this->layoutModel->MemberGroups = $this->modelFacade->get('MemberGroup')
                ->filter('group_id', 'IN', array_values($memberGroupIds))
                ->all();

            return;
        }

        // Check all other layouts for this channel to see if the member group
        // is assigned to a different layout
        $siteId       = $this->siteModel->getProperty('site_id');
        $channelId    = $this->channelModel->getProperty('channel_id');
        $otherLayouts = $this->modelFacade->get('ChannelLayout')
            ->filter('site_id', $siteId)
            ->filter('channel_id', $channelId)
            ->filter('layout_name', '!=', $this->layoutName)
            ->all();

        foreach ($otherLayouts as $otherLayout) {
            /** @var ChannelLayout $otherLayout */

            $needsSave = false;

            /** @var ModelCollection $thisMemberGroups */
            $thisMemberGroups = $otherLayout->MemberGroups;

            $thisMemberGroupIds = [];

            foreach ($thisMemberGroups as $memberGroup) {
                /** @var MemberGroup $memberGroup */

                if (in_array(
                    $memberGroup->getProperty('group_title'),
                    $this->addMemberGroups,
                    true
                )) {
                    $needsSave = true;
                    continue;
                }

                $id = $memberGroup->getProperty('group_id');

                $thisMemberGroupIds[$id] = $id;
            }

            if (! $needsSave) {
                continue;
            }

            if (! $thisMemberGroupIds) {
                $otherLayout->MemberGroups = null;
            } else {
                $otherLayout->MemberGroups = $this->modelFacade->get('MemberGroup')
                    ->filter('group_id', 'IN', array_values($thisMemberGroupIds))
                    ->all();
            }

            $otherLayout->save();
        }

        foreach ($this->addMemberGroups as $addMemberGroupName) {
            $addMemberGroup = $this->modelFacade->get('MemberGroup')
                ->filter('site_id', $siteId)
                ->filter('group_title', $addMemberGroupName)
                ->first();

            if (! $addMemberGroup) {
                continue;
            }

            $id = $addMemberGroup->getProperty('group_id');

            $memberGroupIds[$id] = $id;
        }

        if (! $memberGroupIds) {
            $this->layoutModel->MemberGroups = null;

            return;
        }

        $this->layoutModel->MemberGroups = $this->modelFacade->get('MemberGroup')
            ->filter('group_id', 'IN', array_values($memberGroupIds))
            ->all();
    }
}
