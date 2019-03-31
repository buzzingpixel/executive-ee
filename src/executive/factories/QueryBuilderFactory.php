<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use EllisLab\ExpressionEngine\Service\Database\Query as QueryBuilder;

class QueryBuilderFactory
{
    /**
     * Makes an instance of QueryBuilder
     */
    public function make() : QueryBuilder
    {
        return ee('db');
    }
}
