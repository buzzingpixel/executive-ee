<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services;

use EllisLab\ExpressionEngine\Model\Channel\ChannelField;
use EllisLab\ExpressionEngine\Model\Site\Site as SiteModel;
use EllisLab\ExpressionEngine\Model\Status\Status;
use EllisLab\ExpressionEngine\Service\Model\Collection as ModelCollection;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use Exception;
use function array_keys;
use function array_merge;
use function array_values;
use function in_array;

/**
 * @deprecated This class is trying to do way too much and you can do it all with EE models and things
 */
class ChannelDesignerService
{
    private $modelFacade;

    /**
     * @deprecated This class is trying to do way too much and you can do it all with EE models and things
     */
    public function __construct(ModelFacade $modelFacade)
    {
        $this->modelFacade = $modelFacade;
    }

    private $siteName = 'default_site';

    /**
     * @deprecated This class is trying to do way too much and you can do it all with EE models and things
     */
    public function siteName($str) : self
    {
        $this->siteName = $str;

        return $this;
    }

    private $statuses = [];

    /**
     * @deprecated This class is trying to do way too much and you can do it all with EE models and things
     */
    public function addStatus($status, $color = '000000') : self
    {
        $this->statuses[$status] = $color;

        return $this;
    }

    private $removeStatuses = [];

    /**
     * @deprecated This class is trying to do way too much and you can do it all with EE models and things
     */
    public function removeStatus($status) : self
    {
        $this->removeStatuses[] = $status;

        return $this;
    }

    private $fields = [];

    /**
     * @deprecated This class is trying to do way too much and you can do it all with EE models and things
     */
    public function addField($fieldArray) : self
    {
        $this->fields[] = $fieldArray;

        return $this;
    }

    private $removeFields = [];

    /**
     * @deprecated This class is trying to do way too much and you can do it all with EE models and things
     */
    public function removeField($fieldName) : self
    {
        $this->removeFields[] = $fieldName;

        return $this;
    }

    private $channelName;

    /**
     * @deprecated This class is trying to do way too much and you can do it all with EE models and things
     */
    public function channelName($str) : self
    {
        $this->channelName = $str;

        return $this;
    }

    private $channelTitle;

    /**
     * @deprecated This class is trying to do way too much and you can do it all with EE models and things
     */
    public function channelTitle($str) : self
    {
        $this->channelTitle = $str;

        return $this;
    }

    private $extendedChannelProperties = [];

    /**
     * @deprecated This class is trying to do way too much and you can do it all with EE models and things
     */
    public function extendedChannelProperties($properties) : self
    {
        $this->extendedChannelProperties = $properties;

        return $this;
    }

    /**
     * @deprecated This class is trying to do way too much and you can do it all with EE models and things
     *
     * @throws Exception
     */
    public function save() : void
    {
        $this->setSiteModel();
        $this->addUpdateStatuses();
        $this->addUpdateFields();
        $this->addOrUpdateChannel();
    }

    private $siteModel;

    /**
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

    private function addUpdateStatuses() : void
    {
        if (! $this->statuses) {
            return;
        }

        $order = 3;

        /** @var Status $lastOrder */
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

            $statusModel->set([
                'status' => $status,
                'status_order' => $order,
                'highlight' => $color,
            ]);

            $statusModel->save();

            $order++;
        }
    }

    private function addUpdateFields() : void
    {
        if (! $this->fields) {
            return;
        }

        $siteId = $this->siteModel->getProperty('site_id');

        $presetProperties = ['site_id' => $siteId];

        $defaultProperties = ['field_name' => null];

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

            $_POST = [];
        }
    }

    private function addOrUpdateChannel() : void
    {
        if (! $this->channelName) {
            return;
        }

        $siteId = $this->siteModel->getProperty('site_id');

        $presetProperties = [
            'site_id' => $siteId,
            'channel_name' => $this->channelName,
        ];

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

    private function getCustomFieldsCollection($existingFields = null) : ModelCollection
    {
        $fieldShortNames = [];

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

        $fields = [];

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

    private function getStatusCollection($existingStatuses = null) : ModelCollection
    {
        $requiredStatuses = [
            'open',
            'closed',
        ];

        $requiredStatusModels = $this->modelFacade->get('Status')
            ->filter('status', 'IN', $requiredStatuses)
            ->all();

        $newStatuses = new ModelCollection();

        if ($this->statuses) {
            $newStatuses = $this->modelFacade->get('Status')
                ->filter('status', 'IN', array_keys($this->statuses))
                ->all();
        }

        $statuses = [];

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
