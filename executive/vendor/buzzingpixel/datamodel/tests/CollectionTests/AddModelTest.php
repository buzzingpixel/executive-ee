<?php

namespace CollectionTests;

use PHPUnit\Framework\TestCase;

use BuzzingPixel\DataModel\ModelCollection;
use TestingClasses\ModelInstance;

/**
 * Class ModelCollectionAddModelTest
 * @group collectionTests
 */
class AddModelTest extends TestCase
{
    /**
     * Test add model
     */
    public function testAddModel()
    {
        $collection = new ModelCollection();

        self::assertCount(0, $collection);

        $collection->addModel(new ModelInstance());
        self::assertCount(1, $collection);

        $collection->addModel(new ModelInstance());
        self::assertCount(2, $collection);
    }

    /**
     * Test add models
     */
    public function testAddModels()
    {
        $items = array(new ModelInstance(), new ModelInstance());

        $items2 = array(new ModelInstance(), new ModelInstance(), new ModelInstance());

        $collection = new ModelCollection();

        $collection->addModels($items);
        self::assertCount(2, $collection);

        $collection->addModels($items2);
        self::assertCount(5, $collection);
    }

    /**
     * Test set models
     */
    public function testSetModels()
    {
        $items = array(new ModelInstance(), new ModelInstance());

        $items2 = array(new ModelInstance(), new ModelInstance(), new ModelInstance());

        $collection = new ModelCollection();

        $collection->setModels($items);
        self::assertCount(2, $collection);

        $collection->setModels($items2);
        self::assertCount(3, $collection);
    }
}
