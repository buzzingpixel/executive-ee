<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use buzzingpixel\executive\services\LayoutDesignerService;

class LayoutDesignerServiceFactory
{
    /**
     * Gets a ExtensionDesignerService instance
     */
    public function make() : LayoutDesignerService
    {
        return new LayoutDesignerService(ee('Model'));
    }
}
