<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

namespace BuzzingPixel\Executive\Abstracts;

use BuzzingPixel\Executive\BaseComponent;
use EllisLab\ExpressionEngine\Service\Database\Query as QueryBuilder;

/**
 * Class MigrationService
 */
abstract class BaseMigration extends BaseComponent
{
    /** @var \CI_DB_mysqli_forge $dbForge */
    protected $dbForge;

    /** @var QueryBuilder $queryBuilder */
    protected $queryBuilder;

    /**
     * MigrationService constructor
     */
    public function init()
    {
        $this->dbForge = ee()->dbforge;
        $this->queryBuilder = ee('db');
    }

    /**
     * Safe up
     */
    abstract public function safeUp();
}
