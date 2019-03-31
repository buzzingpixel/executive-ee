<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use buzzingpixel\executive\services\ChannelDesignerService;

class ChannelDesignerServiceFactory
{
    /**
     * Gets a ChannelDesignerService instance
     */
    public function make() : ChannelDesignerService
    {
        return new ChannelDesignerService(ee('Model'));
    }
}
