<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services;

use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use EllisLab\ExpressionEngine\Service\Database\Query as QueryBuilder;
use EllisLab\ExpressionEngine\Model\Addon\Extension as ExtensionModel;

/**
 * Class ExtensionDesignerService
 */
class ExtensionDesignerService
{
    /** @var ModelFacade $modelFacade */
    private $modelFacade;

    /** @var QueryBuilder $queryBuilder */
    private $queryBuilder;

    /**
     * ExtensionDesignerService constructor
     * @param ModelFacade $modelFacade
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(
        ModelFacade $modelFacade,
        QueryBuilder $queryBuilder
    ) {
        $this->modelFacade = $modelFacade;
        $this->queryBuilder = $queryBuilder;
    }

    /** @var string $class */
    private $extClass;

    /**
     * Set the extension class to call
     *
     * @param string $str
     * @return ExtensionDesignerService
     */
    public function extClass($str): self
    {
        $this->extClass = $str;
        return $this;
    }

    /** @var string $extMethod */
    private $extMethod;

    /**
     * Set the extension method to call
     *
     * @param string $str
     * @return ExtensionDesignerService
     */
    public function extMethod($str): self
    {
        $this->extMethod = $str;
        return $this;
    }

    /** @var string $extHook */
    private $extHook;

    /**
     * Set the hook the extension should run on
     *
     * @param string $str
     * @return ExtensionDesignerService
     */
    public function extHook($str): self
    {
        $this->extHook = $str;
        return $this;
    }

    /** @var string $extPriority */
    private $extPriority = 10;

    /**
     * Set the hook the extension should run on
     *
     * @param int $int
     * @return ExtensionDesignerService
     */
    public function extPriority($int): self
    {
        $this->extPriority = (int) $int;
        return $this;
    }

    /**
     * Add the extension
     * @throws \Exception
     */
    public function add(): void
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
            'settings' => '',
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
    public function remove(): void
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
    private function checkPropertiesSet(): void
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
