<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\abstracts;

use BuzzingPixel\Executive\BaseComponent;
use BuzzingPixel\Executive\SchemaDesign\ChannelDesigner;
use BuzzingPixel\Executive\SchemaDesign\ExtensionDesigner;
use BuzzingPixel\Executive\SchemaDesign\LayoutDesigner;
use EllisLab\ExpressionEngine\Service\Database\Query as QueryBuilder;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;

/**
 * Abstract Class BaseMigration
 * @property-read ChannelDesigner $channelDesigner
 * @property-read LayoutDesigner $layoutDesigner
 * @property-read ExtensionDesigner $extensionDesigner
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
    protected function init()
    {
        ee()->load->dbforge();
        $this->dbForge = ee()->dbforge;
        $this->queryBuilder = ee('db');
        $this->modelFacade = ee('Model');
    }

    /**
     * Get channel designer
     * @return ChannelDesigner
     */
    protected function getChannelDesigner()
    {
        return new ChannelDesigner();
    }

    /**
     * Get layout designer
     * @return LayoutDesigner
     */
    protected function getLayoutDesigner()
    {
        return new LayoutDesigner();
    }

    /**
     * Get extension designer
     * @return ExtensionDesigner
     */
    protected function getExtensionDesigner()
    {
        return new ExtensionDesigner();
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
