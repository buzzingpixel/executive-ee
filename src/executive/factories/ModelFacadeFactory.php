<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;

class ModelFacadeFactory
{
    /**
     * Gets an instance of the model facade
     */
    public function make() : ModelFacade
    {
        return ee('Model');
    }
}
