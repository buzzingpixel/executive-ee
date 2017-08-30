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
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;

/**
 * Class MigrationService
 */
abstract class BaseMigration extends BaseComponent
{
    /** @var \CI_DB_mysqli_forge $dbForge */
    protected $dbForge;

    /** @var QueryBuilder $queryBuilder */
    protected $queryBuilder;

    /** @var ModelFacade $modelFacade */
    protected $modelFacade;

    /**
     * MigrationService constructor
     */
    public function init()
    {
        ee()->load->dbforge();
        $this->dbForge = ee()->dbforge;
        $this->queryBuilder = ee('db');
        $this->modelFacade = ee('Model');
    }

    /**
     * Safe up
     */
    abstract public function safeUp();

    /**
     * Safe down
     */
    public function safeDown()
    {
    }
}
