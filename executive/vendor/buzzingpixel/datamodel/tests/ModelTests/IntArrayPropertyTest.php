<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class IntArrayPropertyTest
 * @group modelTests
 */
class IntArrayPropertyTest extends TestCase
{
    /**
     * Test instance model property type
     */
    public function testProperty()
    {
        $model = new ModelInstance();

        $model->intArrayPropTest = 'test';
        self::assertInternalType('array', $model->intArrayPropTest);
        self::assertCount(1, $model->intArrayPropTest);
        self::assertEquals(0, $model->intArrayPropTest[0]);

        $model->intArrayPropTest = 'test|test';
        self::assertInternalType('array', $model->intArrayPropTest);
        self::assertCount(2, $model->intArrayPropTest);
        self::assertInternalType('integer', $model->intArrayPropTest[0]);
        self::assertEquals(0, $model->intArrayPropTest[0]);
        self::assertInternalType('integer', $model->intArrayPropTest[1]);
        self::assertEquals(0, $model->intArrayPropTest[1]);

        $model->intArrayPropTest = '12|test|145asdf';
        self::assertInternalType('array', $model->intArrayPropTest);
        self::assertCount(3, $model->intArrayPropTest);
        self::assertInternalType('integer', $model->intArrayPropTest[0]);
        self::assertEquals(12, $model->intArrayPropTest[0]);
        self::assertInternalType('integer', $model->intArrayPropTest[1]);
        self::assertEquals(0, $model->intArrayPropTest[1]);
        self::assertInternalType('integer', $model->intArrayPropTest[2]);
        self::assertEquals(145, $model->intArrayPropTest[2]);

        $model->intArrayPropTest = array('12345', '14asdf56', 'testing');
        self::assertInternalType('array', $model->intArrayPropTest);
        self::assertCount(3, $model->intArrayPropTest);
        self::assertInternalType('integer', $model->intArrayPropTest[0]);
        self::assertEquals(12345, $model->intArrayPropTest[0]);
        self::assertInternalType('integer', $model->intArrayPropTest[1]);
        self::assertEquals(14, $model->intArrayPropTest[1]);
        self::assertInternalType('integer', $model->intArrayPropTest[2]);
        self::assertEquals(0, $model->intArrayPropTest[2]);
    }
}
