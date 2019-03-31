<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use buzzingpixel\executive\services\ExtensionDesignerService;

class ExtensionDesignerServiceFactory
{
    /**
     * Gets a ExtensionDesignerService instance
     */
    public function make() : ExtensionDesignerService
    {
        return new ExtensionDesignerService(
            ee('Model'),
            ee('db')
        );
    }
}
