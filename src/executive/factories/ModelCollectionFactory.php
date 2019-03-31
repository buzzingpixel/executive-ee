<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use EllisLab\ExpressionEngine\Service\Model\Collection;

class ModelCollectionFactory
{
    public function make(array $elements = []) : Collection
    {
        return new Collection($elements);
    }
}
