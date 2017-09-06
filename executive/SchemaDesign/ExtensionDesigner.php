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

/**
 * Class ExtensionDesigner
 */
class ExtensionDesigner extends BaseComponent
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

    /** @var string $class */
    private $extClass;

    /**
     * Set the extension class to call
     * @param string $str
     * @return $this
     */
    public function extClass($str)
    {
        $this->extClass = str_replace('\\', '\\\\', $str);
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

        /** @var ExtensionModel $extension */
        $extension = $this->modelFacade->make('Extension');

        $extension->set(array(
            'class' => 'Executive_ext',
            'method' => 'userExtensionRouting',
            'hook' => $this->extHook,
            'priority' => $this->extPriority,
            'version' => EXECUTIVE_VER,
            'enabled' => 'y',
            'settings' => array(
                'class' => $this->extClass,
                'method' => $this->extMethod,
            ),
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

        $extensionCandidates = $this->modelFacade->get('Extension')
            ->filter('class', 'Executive_ext')
            ->filter('method', 'userExtensionRouting')
            ->filter('hook', $this->extHook)
            ->all();

        foreach ($extensionCandidates as $candidate) {
            /** @var ExtensionModel $candidate */

            $settings = $candidate->getProperty('settings');

            if ($settings['class'] === $this->extClass &&
                $settings['method'] === $this->extMethod
            ) {
                $candidate->delete();
            }
        }
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
