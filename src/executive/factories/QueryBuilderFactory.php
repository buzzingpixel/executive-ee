<?php
declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use EllisLab\ExpressionEngine\Service\Database\Query as QueryBuilder;

/**
 * Class QueryBuilderFactory
 */
class QueryBuilderFactory
{
    /**
     * Makes an instance of QueryBuilder
     * @return QueryBuilder
     */
    public function make(): QueryBuilder
    {
        return ee('db');
    }
}
