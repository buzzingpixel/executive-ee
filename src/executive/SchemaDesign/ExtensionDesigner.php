<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\SchemaDesign;

use BuzzingPixel\Executive\BaseComponent;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use EllisLab\ExpressionEngine\Model\Addon\Extension as ExtensionModel;
use EllisLab\ExpressionEngine\Service\Database\Query as QueryBuilder;

/**
 * Class ExtensionDesigner
 */
class ExtensionDesigner extends BaseComponent
{
    /** @var ModelFacade $modelFacade */
    private $modelFacade;

    /** @var QueryBuilder $queryBuilder */
    private $queryBuilder;

    /**
     * Initialize class
     */
    protected function init()
    {
        $this->modelFacade = ee('Model');
        $this->queryBuilder = ee('db');
    }

    /** @var string $class */
    private $extClass;

    /**
     * Set the extension class to call
     * @param string $str
     * @return $this
     */
    public function extClass($str)
    {
        $this->extClass = $str;
        return $this;
    }

    /** @var string $extMethod */
    private $extMethod;

    /**
     * Set the extension method to call
     * @param string $str
     * @return $this
     */
    public function extMethod($str)
    {
        $this->extMethod = $str;
        return $this;
    }

    /** @var string $extHook */
    private $extHook;

    /**
     * Set the hook the extension should run on
     * @param string $str
     * @return $this
     */
    public function extHook($str)
    {
        $this->extHook = $str;
        return $this;
    }

    /** @var string $extPriority */
    private $extPriority = 10;

    /**
     * Set the hook the extension should run on
     * @param int $int
     * @return $this
     */
    public function extPriority($int)
    {
        $this->extPriority = (int) $int;
        return $this;
    }

    /**
     * Add the extension
     * @throws \Exception
     */
    public function add()
    {
        $this->checkPropertiesSet();

        if (! $this->extPriority) {
            throw new \Exception(lang('extPriorityRequired'));
        }

        $this->queryBuilder->insert('executive_user_extensions', array(
            'class' => $this->extClass,
            'method' => $this->extMethod,
            'hook' => $this->extHook,
        ));

        /** @var ExtensionModel $extension */
        $extension = $this->modelFacade->make('Extension');

        $extension->set(array(
            'class' => 'Executive_ext',
            'method' => "userExtensionRouting__{$this->queryBuilder->insert_id()}",
            'hook' => $this->extHook,
            'priority' => $this->extPriority,
            'version' => EXECUTIVE_VER,
            'enabled' => 'y',
        ));

        $extension->save();
    }

    /**
     * Remove extension
     * @throws \Exception
     */
    public function remove()
    {
        $this->checkPropertiesSet();

        $row = $this->queryBuilder->select('id')
            ->where('class', $this->extClass)
            ->where('method', $this->extMethod)
            ->where('hook', $this->extHook)
            ->get('executive_user_extensions')
            ->row();

        if (! $row) {
            return;
        }

        $id = (int) $row->id;

        $this->modelFacade->get('Extension')
            ->filter('class', 'Executive_ext')
            ->filter('method', "userExtensionRouting__{$id}")
            ->filter('hook', $this->extHook)
            ->delete();

        $this->queryBuilder->delete('executive_user_extensions', array(
            'id' => $id,
        ));
    }

    /**
     * Check properties set
     * @throws \Exception
     */
    private function checkPropertiesSet()
    {
        if (! $this->extClass) {
            throw new \Exception(lang('extClassRequired'));
        }

        if (! $this->extMethod) {
            throw new \Exception(lang('extMethodRequired'));
        }

        if (! $this->extHook) {
            throw new \Exception(lang('extHookRequired'));
        }
    }
}
