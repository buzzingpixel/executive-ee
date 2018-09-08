<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\factories;

use DI\NotFoundException;
use DI\DependencyException;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\services\ChannelDesignerService;
use buzzingpixel\executive\exceptions\DependencyInjectionBuilderException;

/**
 * Class ChannelDesignerServiceFactory
 */
class ChannelDesignerServiceFactory
{
    /**
     * Gets a ChannelDesignerService instance
     * @return ChannelDesignerService
     * @throws NotFoundException
     * @throws DependencyException
     * @throws DependencyInjectionBuilderException
     */
    public function make(): ChannelDesignerService
    {
        return ExecutiveDi::make(ChannelDesignerService::class);
    }
}
