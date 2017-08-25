<?php

namespace ModelTests;

use BuzzingPixel\DataModel\ModelCollection;
use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class AsArrayTest
 * @group modelTests
 */
class AsArrayTest extends TestCase
{
    /**
     * Test array model property type
     */
    public function test()
    {
        $collectionModelTest1 = new ModelInstance();
        $collectionModelTest2 = new ModelInstance();

        $collectionTest = new ModelCollection(array(
            $collectionModelTest1,
            $collectionModelTest2
        ));

        $model = new ModelInstance(array(
            'mixedProp' => 'test',
            'stringProp' => 'test2',
            'intProp' => '123',
            // 'intPropExtra' => '',
            'floatProp' => 123.4,
            'boolProp' => 'y',
            // 'instanceProp' => '',
            'enumProp' => 'someVal',
            'emailProp' => 'info@buzzingpixel.com',
            'stringArrayPropTest' => 'test1|test123',
            'collectionPropTest' => $collectionTest
        ));

        $asArray = $model->asArray();

        self::assertInternalType('array', $asArray);

        self::assertEquals($model->uuid, $asArray['uuid']);

        self::assertEquals('test', $asArray['mixedProp']);

        self::assertEquals('test2', $asArray['stringProp']);

        self::assertInternalType('integer', $asArray['intProp']);
        self::assertEquals(123, $asArray['intProp']);

        self::assertNull($asArray['intPropExtra']);

        self::assertInternalType('float', $asArray['floatProp']);
        self::assertEquals(123.4, $asArray['floatProp']);

        self::assertInternalType('boolean', $asArray['boolProp']);
        self::assertTrue($asArray['boolProp']);

        self::assertEquals('someVal', $asArray['enumProp']);

        self::assertEquals('info@buzzingpixel.com', $asArray['emailProp']);

        self::assertInternalType('array', $asArray['stringArrayPropTest']);
        self::assertEquals('test1', $asArray['stringArrayPropTest'][0]);
        self::assertEquals('test123', $asArray['stringArrayPropTest'][1]);

        self::assertInternalType('array', $asArray['collectionPropTest']);
        self::assertCount(2, $asArray['collectionPropTest']);
        self::assertInternalType('array', $asArray['collectionPropTest'][$collectionModelTest1->uuid]);
        self::assertInternalType('array', $asArray['collectionPropTest'][$collectionModelTest2->uuid]);
    }
}
