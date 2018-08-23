<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\factories;

use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;

/**
 * Class Service
 */
class ModelFacadeFactory
{
    /**
     * Gets an instance of the model facade
     * @return ModelFacade
     */
    public function make(): ModelFacade
    {
        return ee('Model');
    }
}
