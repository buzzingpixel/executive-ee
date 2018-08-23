<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\factories;

use DI\NotFoundException;
use DI\DependencyException;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\services\LayoutDesignerService;
use buzzingpixel\executive\exceptions\DependencyInjectionBuilderException;

/**
 * Class LayoutDesignerServiceFactory
 */
class LayoutDesignerServiceFactory
{
    /**
     * Gets a ExtensionDesignerService instance
     * @return LayoutDesignerService
     * @throws NotFoundException
     * @throws DependencyException
     * @throws DependencyInjectionBuilderException
     */
    public function make(): LayoutDesignerService
    {
        return ExecutiveDi::get(LayoutDesignerService::class);
    }
}
