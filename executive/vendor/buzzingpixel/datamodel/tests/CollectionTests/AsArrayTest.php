<?php

namespace CollectionTests;

use PHPUnit\Framework\TestCase;

use BuzzingPixel\DataModel\ModelCollection;
use TestingClasses\ModelInstance;

/**
 * Class AsArrayTest
 * @group collectionTests
 */
class AsArrayTest extends TestCase
{
    /**
     * Test
     */
    public function testAsArray()
    {
        $model1 = new ModelInstance(array(
            'mixedProp' => 'test',
            'stringProp' => 'test2',
            'intProp' => '123',
            // 'intPropExtra' => '',
            'floatProp' => 123.4,
            'boolProp' => 'y',
            // 'instanceProp' => '',
            'enumProp' => 'someVal',
            'emailProp' => 'info@buzzingpixel.com',
            'stringArrayPropTest' => 'test1|test123'
        ));

        $model2 = new ModelInstance(array(
            'mixedProp' => 'test12',
            'stringProp' => 'test245',
            'intProp' => '12343',
            // 'intPropExtra' => '',
            'floatProp' => 123.8,
            'boolProp' => 'no',
            // 'instanceProp' => '',
            'enumProp' => 'someValue',
            'emailProp' => 'testing@buzzingpixel.com',
            'stringArrayPropTest' => 'test3|test1235'
        ));

        $collection = new ModelCollection(array(
            $model1,
            $model2
        ));

        $asArray = $collection->asArray();

        self::assertInternalType('array', $asArray);
        self::assertCount(2, $asArray);
        self::assertInternalType('array', $asArray[$model1->uuid]);
        self::assertInternalType('array', $asArray[$model2->uuid]);
        self::assertEquals($model1->asArray(), $asArray[$model1->uuid]);
        self::assertEquals($model2->asArray(), $asArray[$model2->uuid]);
    }
}
