<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;
use TestingClasses\TestingClass;
use TestingClasses\TestingClass2;

/**
 * Class ArrayPropertyTest
 * @group modelTests
 */
class ArrayPropertyTest extends TestCase
{
    /**
     * Test array model property type
     */
    public function testProperty()
    {
        $testClass1 = new TestingClass();
        $testClass2 = new TestingClass2();

        $model = new ModelInstance();

        $model->arrayPropTest = 'test';
        self::assertInternalType('array', $model->arrayPropTest);
        self::assertCount(1, $model->arrayPropTest);
        self::assertEquals(array('test'), $model->arrayPropTest);

        $model->arrayPropTest = 'test|test2';
        self::assertInternalType('array', $model->arrayPropTest);
        self::assertCount(2, $model->arrayPropTest);
        self::assertEquals(array('test', 'test2'), $model->arrayPropTest);

        $model->arrayPropTest = $testClass1;
        self::assertInternalType('array', $model->arrayPropTest);
        self::assertCount(1, $model->arrayPropTest);
        self::assertInstanceOf('\TestingClasses\TestingClass', $model->arrayPropTest[0]);

        $model->arrayPropTest = array($testClass1, $testClass2);
        self::assertInternalType('array', $model->arrayPropTest);
        self::assertCount(2, $model->arrayPropTest);
        self::assertInstanceOf('\TestingClasses\TestingClass', $model->arrayPropTest[0]);
        self::assertInstanceOf('\TestingClasses\TestingClass2', $model->arrayPropTest[1]);
    }
}
