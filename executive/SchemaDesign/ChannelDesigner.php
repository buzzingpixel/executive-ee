<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\SchemaDesign;

use BuzzingPixel\Executive\BaseComponent;
use EllisLab\ExpressionEngine\Model\Channel\ChannelField;
use EllisLab\ExpressionEngine\Model\Site\Site as SiteModel;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use EllisLab\ExpressionEngine\Model\Status\StatusGroup as StatusGroupModel;
use EllisLab\ExpressionEngine\Model\Channel\ChannelFieldGroup as ChannelFieldGroupModel;

/**
 * Class ChannelDesigner
 */
class ChannelDesigner extends BaseComponent
{
    /** @var ModelFacade $modelFacade */
    private $modelFacade;

    /**
     * Initialize class
     */
    protected function init()
    {
        $this->modelFacade = ee('Model');
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

    /** @var string $statusGroupName */
    private $statusGroupName = 'Default';

    /**
     * Set the status group name
     * @param string $str
     * @return self
     */
    public function statusGroupName($str)
    {
        $this->statusGroupName = $str;
        return $this;
    }

    /** @var array $statuses */
    private $statuses = array();

    /**
     * Add status
     * @param string $status
     * @param string $color
     * @return self
     */
    public function addStatus($status, $color = '000000')
    {
        $this->statuses[$status] = $color;
        return $this;
    }

    /** @var string $fieldGroupName */
    private $fieldGroupName;

    /**
     * Set the field group name
     * @param string $str
     * @return self
     */
    public function fieldGroupName($str)
    {
        $this->fieldGroupName = $str;
        return $this;
    }

    /** @var array $fields */
    private $fields = array();

    /**
     * Add field
     * @param array $fieldArray
     * @return self
     */
    public function addField($fieldArray)
    {
        $this->fields[] = $fieldArray;
        return $this;
    }

    /** @var string $channelName */
    private $channelName;

    /**
     * Add channel name
     * @param string $str
     * @return self
     */
    public function channelName($str)
    {
        $this->channelName = $str;
        return $this;
    }

    /** @var string $channelTitle */
    private $channelTitle;

    /**
     * Add channel title
     * @param string $str
     * @return self
     */
    public function channelTitle($str)
    {
        $this->channelTitle = $str;
        return $this;
    }

    /** @var array $extendedChannelProperties */
    private $extendedChannelProperties = array();

    /**
     * Set extended channel properties
     * @param array $properties
     * @return self
     */
    public function extendedChannelProperties($properties)
    {
        $this->extendedChannelProperties = $properties;
        return $this;
    }

    /**
     * Save schema design
     */
    public function save()
    {
        $this->setSiteModel();
        $this->setStatusGroupModel();
        $this->addUpdateStatuses();
        $this->setFieldGroup();
        $this->addUpdateFields();
        $this->addOrUpdateChannel();
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

    /** @var StatusGroupModel $statusGroupModel */
    private $statusGroupModel;

    /**
     * Set status group model
     */
    private function setStatusGroupModel()
    {
        if (! $this->statusGroupName) {
            return;
        }

        $siteId = $this->siteModel->getProperty('site_id');

        $statusGroup = $this->modelFacade->get('StatusGroup')
            ->filter('site_id', $siteId)
            ->filter('group_name', $this->statusGroupName)
            ->first();

        if (! $statusGroup) {
            $statusGroup = $this->modelFacade->make('StatusGroup');

            $statusGroup->set(array(
                'site_id' => $siteId,
                'group_name' => $this->statusGroupName,
            ));

            $statusGroup->save();

            $groupId = $statusGroup->getProperty('group_id');

            $open = $this->modelFacade->make('Status');
            $open->set(array(
                'site_id' => $siteId,
                'group_id' => $groupId,
                'status' => 'open',
                'status_order' => 1,
                'highlight' => '009933',
            ));
            $open->save();

            $closed = $this->modelFacade->make('Status');
            $closed->set(array(
                'site_id' => $siteId,
                'group_id' => $groupId,
                'status' => 'closed',
                'status_order' => 2,
                'highlight' => '990000',
            ));
            $closed->save();
        }

        $this->statusGroupModel = $statusGroup;
    }

    /**
     * Add or update statuses
     */
    private function addUpdateStatuses()
    {
        if (! $this->statusGroupModel || ! $this->statuses) {
            return;
        }

        $siteId = $this->siteModel->getProperty('site_id');

        $groupId = $this->statusGroupModel->getProperty('group_id');

        $order = 3;

        $lastOrder = $this->modelFacade->get('Status')
            ->filter('site_id', $siteId)
            ->filter('group_id', $groupId)
            ->order('status_order', 'desc')
            ->first();

        if ($lastOrder) {
            $order = $lastOrder->getProperty('status_order') + 1;
        }

        foreach ($this->statuses as $status => $color) {
            $statusModel = $this->modelFacade->get('Status')
                ->filter('site_id', $siteId)
                ->filter('group_id', $groupId)
                ->filter('status', $status)
                ->first();

            if (! $statusModel) {
                $statusModel = $this->modelFacade->make('Status');
            }

            $statusModel->set(array(
                'site_id' => $siteId,
                'group_id' => $groupId,
                'status' => $status,
                'status_order' => $order,
                'highlight' => $color,
            ));

            $statusModel->save();

            $order++;
        }
    }

    /** @var ChannelFieldGroupModel $fieldGroupModel */
    private $fieldGroupModel;

    /**
     * Set field group
     */
    private function setFieldGroup()
    {
        if (! $this->fieldGroupName) {
            return;
        }

        $siteId = $this->siteModel->getProperty('site_id');

        $fieldGroup = $this->modelFacade->get('ChannelFieldGroup')
            ->filter('site_id', $siteId)
            ->filter('group_name', $this->fieldGroupName)
            ->first();

        if (! $fieldGroup) {
            $fieldGroup = $this->modelFacade->make('ChannelFieldGroup');

            $fieldGroup->set(array(
                'site_id' => $siteId,
                'group_name' => $this->fieldGroupName,
            ));

            $fieldGroup->save();
        }

        $this->fieldGroupModel = $fieldGroup;
    }

    /**
     * Add or update fields
     */
    private function addUpdateFields()
    {
        if (! $this->fieldGroupModel || ! $this->fields) {
            return;
        }

        $siteId = $this->siteModel->getProperty('site_id');

        $groupId = $this->fieldGroupModel->getProperty('group_id');

        $presetProperties = array(
            'site_id' => $siteId,
            'group_id' => $groupId,
        );

        $defaultProperties = array(
            'field_name' => null,
            'field_label' => null,
            'field_type' => 'text',
            'field_required' => false,
            'field_settings' => array(
                'field_maxl' => '256',
                'field_content_type' => 'all',
                'field_show_smileys' => 'n',
                'field_show_file_selector' => 'n',
            ),
        );

        foreach ($this->fields as $field) {
            if (! isset($field['field_name'])) {
                continue;
            }

            /** @var ChannelField $fieldModel */
            $fieldModel = $this->modelFacade->get('ChannelField')
                ->filter('site_id', $siteId)
                ->filter('group_id', $groupId)
                ->filter('field_name', $field['field_name'])
                ->first();

            if (! $fieldModel) {
                if (! isset($field['field_label'])) {
                    $field['field_label'] = $field['field_name'];
                }

                $fieldModel = $this->modelFacade->make('ChannelField');
            }

            $fieldData = array_merge(
                $defaultProperties,
                $field,
                $presetProperties
            );

            $_POST = $fieldData;

            $fieldModel->set($fieldData);

            $fieldModel->save();

            $fieldModel->emit('afterUpdate', array(
                'field_id' => $fieldModel->getProperty('field_id'),
                'group_id' => $groupId,
            ));

            $_POST = array();
        }
    }

    /**
     * Add or update channel
     */
    private function addOrUpdateChannel()
    {
        if (! $this->channelName) {
            return;
        }

        $siteId = $this->siteModel->getProperty('site_id');

        $presetProperties = array(
            'site_id' => $siteId,
            'channel_name' => $this->channelName,
        );

        $channelModel = $this->modelFacade->get('Channel')
            ->filter('site_id', $siteId)
            ->filter('channel_name', $this->channelName)
            ->first();

        if (! $channelModel) {
            if (! $this->channelTitle) {
                $this->channelTitle = $this->channelName;
            }

            if (! isset($this->extendedChannelProperties['deft_status'])) {
                $presetProperties['deft_status'] = 'open';
            }

            $channelModel = $this->modelFacade->make('Channel');
        }

        if ($this->channelTitle) {
            $presetProperties['channel_title'] = $this->channelTitle;
        }

        if ($this->statusGroupModel) {
            $statusGroupId = $this->statusGroupModel->getProperty('group_id');
            $presetProperties['status_group'] = $statusGroupId;
        }

        if ($this->fieldGroupModel) {
            $fieldGroupId = $this->fieldGroupModel->getProperty('group_id');
            $presetProperties['field_group'] = $fieldGroupId;
        }

        $channelModel->set(array_merge(
            $this->extendedChannelProperties,
            $presetProperties
        ));

        $channelModel->save();
    }
}
