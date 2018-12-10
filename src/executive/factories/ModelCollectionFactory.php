<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\factories;

use EllisLab\ExpressionEngine\Service\Model\Collection;

class ModelCollectionFactory
{
    public function make(array $elements = []): Collection
    {
        return new Collection($elements);
    }
}
