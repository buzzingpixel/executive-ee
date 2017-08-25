<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;
use TestingClasses\TestingClass;
use BuzzingPixel\DataModel\ModelCollection;

/**
 * Class CollectionPropertyTest
 * @group modelTests
 */
class CollectionPropertyTest extends TestCase
{
    /**
     * Test model property
     */
    public function testProperty()
    {
        $model = new ModelInstance();

        self::assertNull($model->collectionPropTest);

        $model->collectionPropTest = 'asdf';
        self::assertNull($model->collectionPropTest);

        $model->collectionPropTest = new TestingClass();
        self::assertNull($model->collectionPropTest);

        $model->collectionPropTest = new ModelCollection();
        self::assertInstanceOf('\BuzzingPixel\DataModel\ModelCollection', $model->collectionPropTest);
    }
}
