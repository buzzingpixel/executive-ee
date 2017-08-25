<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class FloatArrayPropertyTest
 * @group modelTests
 */
class FloatArrayPropertyTest extends TestCase
{
    /**
     * Test instance model property type
     */
    public function testProperty()
    {
        $model = new ModelInstance();

        $model->floatArrayPropTest = 'test';
        self::assertInternalType('array', $model->floatArrayPropTest);
        self::assertCount(1, $model->floatArrayPropTest);
        self::assertEquals(0, $model->floatArrayPropTest[0]);

        $model->floatArrayPropTest = 'test|test';
        self::assertInternalType('array', $model->floatArrayPropTest);
        self::assertCount(2, $model->floatArrayPropTest);
        self::assertInternalType('float', $model->floatArrayPropTest[0]);
        self::assertEquals(0, $model->floatArrayPropTest[0]);
        self::assertInternalType('float', $model->floatArrayPropTest[1]);
        self::assertEquals(0, $model->floatArrayPropTest[1]);

        $model->floatArrayPropTest = '12.3|test|145asdf';
        self::assertInternalType('array', $model->floatArrayPropTest);
        self::assertCount(3, $model->floatArrayPropTest);
        self::assertInternalType('float', $model->floatArrayPropTest[0]);
        self::assertEquals(12.3, $model->floatArrayPropTest[0]);
        self::assertInternalType('float', $model->floatArrayPropTest[1]);
        self::assertEquals(0, $model->floatArrayPropTest[1]);
        self::assertInternalType('float', $model->floatArrayPropTest[2]);
        self::assertEquals(145, $model->floatArrayPropTest[2]);

        $model->floatArrayPropTest = array('12345', '14asdf56', 'testing');
        self::assertInternalType('array', $model->floatArrayPropTest);
        self::assertCount(3, $model->floatArrayPropTest);
        self::assertInternalType('float', $model->floatArrayPropTest[0]);
        self::assertEquals(12345, $model->floatArrayPropTest[0]);
        self::assertInternalType('float', $model->floatArrayPropTest[1]);
        self::assertEquals(14, $model->floatArrayPropTest[1]);
        self::assertInternalType('float', $model->floatArrayPropTest[2]);
        self::assertEquals(0, $model->floatArrayPropTest[2]);
    }
}
