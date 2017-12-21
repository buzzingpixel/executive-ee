<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\SchemaDesign;

use Exception;
use BuzzingPixel\Executive\BaseComponent;
use EllisLab\ExpressionEngine\Model\Channel\ChannelField;
use EllisLab\ExpressionEngine\Model\Site\Site as SiteModel;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use EllisLab\ExpressionEngine\Service\Model\Collection as ModelCollection;

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

    /** @var array $removeStatuses */
    private $removeStatuses = array();

    /**
     * Remove a status
     * @param $status
     * @return $this
     */
    public function removeStatus($status)
    {
        $this->removeStatuses[] = $status;
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

    /** @var array $removeFields */
    private $removeFields = array();

    /**
     * Remove field
     * @param $fieldName
     * @return $this
     */
    public function removeField($fieldName)
    {
        $this->removeFields[] = $fieldName;
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
     * @throws Exception
     */
    public function save()
    {
        $this->setSiteModel();
        $this->addUpdateStatuses();
        $this->addUpdateFields();
        $this->addOrUpdateChannel();
    }

    /** @var SiteModel $siteModel */
    private $siteModel;

    /**
     * Set site model
     * @throws Exception
     */
    private function setSiteModel()
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

    /**
     * Add or update statuses
     */
    private function addUpdateStatuses()
    {
        if (! $this->statuses) {
            return;
        }

        $order = 3;

        $lastOrder = $this->modelFacade->get('Status')
            ->order('status_order', 'desc')
            ->first();

        if ($lastOrder) {
            $order = $lastOrder->getProperty('status_order') + 1;
        }

        foreach ($this->statuses as $status => $color) {
            $statusModel = $this->modelFacade->get('Status')
                ->filter('status', $status)
                ->first();

            if (! $statusModel) {
                $statusModel = $this->modelFacade->make('Status');
            }

            $statusModel->set(array(
                'status' => $status,
                'status_order' => $order,
                'highlight' => $color,
            ));

            $statusModel->save();

            $order++;
        }
    }

    /**
     * Add or update fields
     */
    private function addUpdateFields()
    {
        if (! $this->fields) {
            return;
        }

        $siteId = $this->siteModel->getProperty('site_id');

        $presetProperties = array(
            'site_id' => $siteId,
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

        $channelModel->set(array_merge(
            $this->extendedChannelProperties,
            $presetProperties
        ));

        $channelModel->CustomFields = $this->getCustomFieldsCollection(
            $channelModel->CustomFields
        );

        $channelModel->Statuses = $this->getStatusCollection(
            $channelModel->Statuses
        );

        $channelModel->save();
    }

    /**
     * Gets the custom fields
     * @param ModelCollection $existingFields
     * @return ModelCollection
     */
    private function getCustomFieldsCollection($existingFields = null)
    {
        $fieldShortNames = array();

        foreach ($this->fields as $field) {
            if (! isset($field['field_name'])) {
                continue;
            }

            $fieldShortNames[] = $field['field_name'];
        }

        $newFields = new ModelCollection();

        if ($fieldShortNames) {
            $newFields = $this->modelFacade->get('ChannelField')
                ->filter('field_name', 'IN', $fieldShortNames)
                ->all();
        }

        $fields = array();

        if ($existingFields) {
            foreach ($existingFields as $field) {
                if (in_array($field->field_name, $this->removeFields, false)) {
                    continue;
                }

                $fields[$field->field_name] = $field;
            }
        }

        foreach ($newFields as $field) {
            $fields[$field->field_name] = $field;
        }

        return new ModelCollection(array_values($fields));
    }

    /**
     * Gets the status collection
     * @param ModelCollection $existingStatuses
     * @return ModelCollection
     */
    private function getStatusCollection($existingStatuses = null)
    {
        $requiredStatuses = array(
            'open',
            'closed',
        );

        $requiredStatusModels = $this->modelFacade->get('Status')
            ->filter('status', 'IN', $requiredStatuses)
            ->all();

        $newStatuses = new ModelCollection();

        if ($this->statuses) {
            $newStatuses = $this->modelFacade->get('Status')
                ->filter('status', 'IN', array_keys($this->statuses))
                ->all();
        }

        $statuses = array();

        foreach ($requiredStatusModels as $status) {
            $statuses[$status->status] = $status;
        }

        if ($existingStatuses) {
            foreach ($existingStatuses as $status) {
                if (in_array($status->status, $this->removeStatuses, false)) {
                    continue;
                }

                $statuses[$status->status] = $status;
            }
        }

        foreach ($newStatuses as $status) {
            $statuses[$status->status] = $status;
        }

        return new ModelCollection(array_values($statuses));
    }
}
