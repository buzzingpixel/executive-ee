<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\abstracts;

use buzzingpixel\executive\factories\DBForgeFactory;
use buzzingpixel\executive\factories\ModelFacadeFactory;
use buzzingpixel\executive\interfaces\MigrationInterface;
use buzzingpixel\executive\factories\QueryBuilderFactory;
use buzzingpixel\executive\factories\LayoutDesignerServiceFactory;
use buzzingpixel\executive\factories\ChannelDesignerServiceFactory;
use buzzingpixel\executive\factories\ExtensionDesignerServiceFactory;

/**
 * Abstract Class MigrationAbstract
 */
abstract class MigrationAbstract implements MigrationInterface
{
    /** @var DBForgeFactory $dbForgeFactory */
    protected $dbForgeFactory;

    /** @var QueryBuilderFactory $queryBuilderFactory */
    protected $queryBuilderFactory;

    /** @var ModelFacadeFactory $modelFacadeFactory */
    protected $modelFacadeFactory;

    /** @var ChannelDesignerServiceFactory $channelDesignerFactory */
    protected $channelDesignerFactory;

    /** @var ExtensionDesignerServiceFactory $extensionDesignerFactory */
    protected $extensionDesignerFactory;

    /** @var LayoutDesignerServiceFactory $layoutDesignerFactory */
    protected $layoutDesignerFactory;

    /**
     * MigrationAbstract constructor
     * @param DBForgeFactory $dbForgeFactory
     * @param QueryBuilderFactory $queryBuilderFactory
     * @param ModelFacadeFactory $modelFacadeFactory
     * @param ChannelDesignerServiceFactory $channelDesignerFactory
     * @param ExtensionDesignerServiceFactory $extensionDesignerFactory
     * @param LayoutDesignerServiceFactory $layoutDesignerFactory
     */
    public function __construct(
        DBForgeFactory $dbForgeFactory = null,
        QueryBuilderFactory $queryBuilderFactory = null,
        ModelFacadeFactory $modelFacadeFactory = null,
        ChannelDesignerServiceFactory $channelDesignerFactory = null,
        ExtensionDesignerServiceFactory $extensionDesignerFactory = null,
        LayoutDesignerServiceFactory $layoutDesignerFactory = null
    ) {
        $this->dbForgeFactory = $dbForgeFactory ?: new DBForgeFactory();

        $this->queryBuilderFactory = $queryBuilderFactory ?:
            new QueryBuilderFactory();

        $this->modelFacadeFactory = $modelFacadeFactory ?:
            new ModelFacadeFactory();

        $this->channelDesignerFactory = $channelDesignerFactory ?:
            new ChannelDesignerServiceFactory();

        $this->extensionDesignerFactory = $extensionDesignerFactory ?:
            new ExtensionDesignerServiceFactory();

        $this->layoutDesignerFactory = $layoutDesignerFactory ?:
            new LayoutDesignerServiceFactory();
    }
}
