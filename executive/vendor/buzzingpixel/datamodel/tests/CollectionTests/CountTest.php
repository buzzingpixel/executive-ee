<?php

namespace CollectionTests;

use PHPUnit\Framework\TestCase;

use BuzzingPixel\DataModel\ModelCollection;
use TestingClasses\ModelInstance;

/**
 * Class CountTest
 * @group collectionTests
 */
class CountTest extends TestCase
{
    /**
     * Test count
     */
    public function testCount()
    {
        $items = array(new ModelInstance(), new ModelInstance());

        $collection = new ModelCollection($items);

        self::assertCount(2, $collection);
        self::assertEquals(2, $collection->count());

        $items[] = new ModelInstance();
        $collection->setModels($items);

        self::assertCount(3, $collection);
        self::assertEquals(3, $collection->count());

        $collection->setModels(array());

        self::assertCount(0, $collection);
        self::assertEquals(0, $collection->count());
    }
}
