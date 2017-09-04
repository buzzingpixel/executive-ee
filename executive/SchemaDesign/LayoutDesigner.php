<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\SchemaDesign;

use BuzzingPixel\Executive\BaseComponent;
use EllisLab\ExpressionEngine\Model\Channel\ChannelLayout;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use EllisLab\ExpressionEngine\Model\Site\Site as SiteModel;
use EllisLab\ExpressionEngine\Model\Channel\Channel as ChannelModel;
use EllisLab\ExpressionEngine\Model\Channel\ChannelField as ChannelFieldModel;
use EllisLab\ExpressionEngine\Service\Model\Collection as ModelCollection;
use EllisLab\ExpressionEngine\Model\Channel\Display\DefaultChannelLayout;
use EllisLab\ExpressionEngine\Model\Member\MemberGroup;

/**
 * Class LayoutDesigner
 */
class LayoutDesigner extends BaseComponent
{
    /** @var ModelFacade $modelFacade */
    private $modelFacade;

    /** @var array $fieldNameToIdMap */
    private $fieldNameToIdMap = array();

    /** @var array $fieldIdStrToNameMap */
    private $fieldIdStrToNameMap = array();

    /** @var array $requiredFieldMap */
    private $requiredFieldMap = array(
        'title' => true,
        'url_title' => true,
        'entry_date' => true,
    );

    /**
     * Initialize class
     */
    protected function init()
    {
        $this->modelFacade = ee('Model');

        $fields = $this->modelFacade->get('ChannelField')->all();

        foreach ($fields as $field) {
            /** @var ChannelFieldModel $field */

            $fieldId = $field->getProperty('field_id');
            $fieldIdStr = "field_id_{$fieldId}";
            $fieldName = $field->getProperty('field_name');

            $this->fieldNameToIdMap[$fieldName] = $fieldId;
            $this->fieldIdStrToNameMap[$fieldIdStr] = $fieldIdStr;
            $this->requiredFieldMap[$fieldIdStr] = $field->getProperty(
                'field_required'
            );
            $this->requiredFieldMap['title'] = true;
            $this->requiredFieldMap['url_title'] = true;
            $this->requiredFieldMap['entry_date'] = true;
        }
    }

    /** @var string $siteName */
    private $siteName = 'default_site';

    /**
     * Set the site name
     * @param string $str
     * @return self
     */
    public function siteName($str)
    {
        $this->siteName = $str;
        return $this;
    }

    /** @var string $channel */
    private $channel;

    /**
     * Set the channel
     * @param string $str
     * @return self
     */
    public function channel($str)
    {
        $this->channel = $str;
        return $this;
    }

    /** @var string $layoutName */
    private $layoutName;

    /**
     * Set the layout name
     * @param string $str
     * @return self
     */
    public function layoutName($str)
    {
        $this->layoutName = $str;
        return $this;
    }

    /** @var array $memberGroups */
    private $addMemberGroups = array();

    /**
     * Add member group
     * @param string $str
     * @return self
     */
    public function addMemberGroup($str)
    {
        $this->addMemberGroups[] = $str;
        return $this;
    }

    /** @var array $memberGroups */
    private $removeMemberGroups = array();

    /**
     * Remove member group
     * @param string $str
     * @return self
     */
    public function removeMemberGroup($str)
    {
        $this->removeMemberGroups[] = $str;
        return $this;
    }

    /** @var string $tab */
    private $tab = 'Publish';

    /**
     * Set the tab to add fields to
     * @param string $str
     * @return self
     */
    public function tab($str)
    {
        $this->tab = $str;
        return $this;
    }

    /** @var array $removeTabs */
    private $removeTabs = array();

    /**
     * Remove a tab
     * @param string $str
     * @return self
     */
    public function removeTab($str)
    {
        $this->removeTabs[] = $str;
        return $this;
    }

    /** @var array $tabVisibility */
    private $tabVisibility = array();

    /**
     * Set whether the current working tab is visible
     * @param bool $val
     * @return self
     */
    public function tabIsVisible($val = true)
    {
        $this->tabVisibility[$this->tab] = $val;
        return $this;
    }

    /** @var array $tabFields */
    private $tabFields = array();

    /** @var string $currentField */
    private $currentField;

    /**
     * Add field to current tab
     * @param string $str
     * @return self
     */
    public function addField($str)
    {
        $this->currentField = $str;
        $this->tabFields[$this->tab][] = $str;
        return $this;
    }

    /** @var array $fieldVisibility */
    private $fieldVisibility = array();

    /**
     * Set whether the current field is visible
     * @param bool $val
     * @return self
     */
    public function fieldIsVisible($val = true)
    {
        $this->fieldVisibility[$this->currentField] = $val;
        return $this;
    }

    /** @var array $fieldCollapsed */
    private $fieldCollapsed = array();

    /**
     * Set whether the current field is collapsed
     * @param bool $val
     * @return self
     */
    public function fieldIsCollapsed($val = true)
    {
        $this->fieldCollapsed[$this->currentField] = $val;
        return $this;
    }

    /**
     * Save schema design
     * @throws \Exception
     */
    public function save()
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
     * @throws \Exception
     */
    private function setSiteModel()
    {
        if (! $this->siteName) {
            throw new \Exception('Site name not defined');
        }

        /** @var SiteModel $site */
        $this->siteModel = $this->modelFacade->get('Site')
            ->filter('site_name', $this->siteName)
            ->first();

        if (! $this->siteModel) {
            throw new \Exception('Site not found');
        }
    }

    /** @var ChannelModel $siteModel */
    private $channelModel;

    /**
     * Set Channel Model
     * @throws \Exception
     */
    private function setChannelModel()
    {
        if (! $this->channel) {
            throw new \Exception('Channel not defined');
        }

        /** @var SiteModel $site */
        $this->channelModel = $this->modelFacade->get('Channel')
            ->filter('site_id', $this->siteModel->getProperty('site_id'))
            ->filter('channel_name', $this->channel)
            ->first();

        if (! $this->channelModel) {
            throw new \Exception('Channel not found');
        }
    }

    /** @var ChannelLayout $layoutModel */
    private $layoutModel;

    /**
     * Set layout model
     * @throws \Exception
     */
    private function setLayoutModel()
    {
        if (! $this->layoutName) {
            throw new \Exception('Layout name not defined');
        }

        $siteId = $this->siteModel->getProperty('site_id');
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
    private function processTabsAndFields()
    {
        // Start variables
        $placedFieldIds = array();
        $firstTabId = 'publish';

        // The publish tab is always first and always visible
        $tabs = array(
            'publish' => array(
                'id' => 'publish',
                'name' => 'publish',
                'visible' => true,
                'fields' => array(),
            )
        );

        // Iterate through user set tab fields
        foreach ($this->tabFields as $tab => $fieldNames) {
            /** @var array $fieldNames */

            // Create the tab ID
            $tabId = str_replace(' ', '_', strtolower($tab));

            // If the tab ID is publish, it cannot be hidden
            if ($tabId === 'publish') {
                $this->tabVisibility[$tab] = true;
            }

            // Create the tab
            $tabs[$tabId] = array(
                'id' => $tabId,
                'name' => $tab,
                'visible' => isset($this->tabVisibility[$tab]) ?
                    $this->tabVisibility[$tab] !== false :
                    true,
                'fields' => array(),
            );

            // Iterate through the field names
            foreach ($fieldNames as $fieldName) {
                // Set default field string
                $fieldIdStr = $fieldName;

                // Get the field ID
                $fieldId = isset($this->fieldNameToIdMap[$fieldName]) ?
                    $this->fieldNameToIdMap[$fieldName] :
                    null;

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
                $tabs[$tabId]['fields'][] = array(
                    'field' => $fieldIdStr,
                    'visible' => isset($this->fieldVisibility[$fieldName]) ?
                        $this->fieldVisibility[$fieldName] !== false :
                        true,
                    'collapsed' => isset($this->fieldCollapsed[$fieldName]) ?
                        $this->fieldCollapsed[$fieldName] === true :
                        false,
                );

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
            $tabs[$tabId] = array(
                'id' => $tabId,
                'name' => $layoutTab['name'],
                'visible' => isset($this->tabVisibility[$layoutTab['name']]) ?
                    $this->tabVisibility[$layoutTab['name']] !== false :
                    $layoutTab['visible'],
                'fields' => array(),
            );
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
                $fieldName = isset($this->fieldIdStrToNameMap[$fieldIdStr]) ?
                    $this->fieldIdStrToNameMap[$fieldIdStr] :
                    $fieldIdStr;

                // If the field is required, make sure it is visible
                if (isset($this->requiredFieldMap[$fieldIdStr]) &&
                    $this->requiredFieldMap[$fieldIdStr]
                ) {
                    $this->fieldVisibility[$fieldName] = true;
                }

                // Add the field to the fields array on the tab
                $tabs[$tabId]['fields'][] = array(
                    'field' => $fieldIdStr,
                    'visible' => isset($this->fieldVisibility[$fieldName]) ?
                        $this->fieldVisibility[$fieldName] !== false :
                        $field['visible'],
                    'collapsed' => isset($this->fieldCollapsed[$fieldName]) ?
                        $this->fieldCollapsed[$fieldName] === true :
                        $field['collapsed'],
                );

                $placedFieldIds[$fieldIdStr] = $fieldIdStr;
            }
        }

        // Go through this channel's fields and make sure they have been set
        /** @var ModelCollection $channelFields */
        $channelFields = $this->channelModel->FieldGroup->ChannelFields;
        foreach ($channelFields as $field) {
            /** @var ChannelFieldModel $field */
            $fieldIdStr = "field_id_{$field->getProperty('field_id')}";

            // If the field is set, we have nothing to do here
            if (isset($placedFieldIds[$fieldIdStr])) {
                continue;
            }

            // Add the field to the fields array on the tab
            $tabs[$firstTabId]['fields'][] = array(
                'field' => $fieldIdStr,
                'visible' => false,
                'collapsed' => false,
            );
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
                $tabs[$tabId] = array(
                    'id' => $layoutTab['id'],
                    'name' => $layoutTab['name'],
                    'visible' => false,
                    'fields' => array(),
                );
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
    private function processMemberGroups()
    {
        // Get the member groups
        /** @var ModelCollection $memberGroups */
        $memberGroups = $this->layoutModel->MemberGroups;

        $memberGroupIds = array();

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
        $siteId = $this->siteModel->getProperty('site_id');
        $channelId = $this->channelModel->getProperty('channel_id');
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

            $thisMemberGroupIds = array();

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
