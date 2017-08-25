<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;
use TestingClasses\TestingClass;
use TestingClasses\TestingClass2;

/**
 * Class InstanceArrayPropertyTest
 * @group modelTests
 */
class InstanceArrayPropertyTest extends TestCase
{
    /**
     * Test instance model property type
     */
    public function testProperty()
    {
        $model = new ModelInstance();

        $model->instanceArrayPropTest = 'test';
        self::assertInternalType('array', $model->instanceArrayPropTest);
        self::assertCount(1, $model->instanceArrayPropTest);
        self::assertEmpty($model->instanceArrayPropTest[0]);

        $model->instanceArrayPropTest = 'test|test';
        self::assertInternalType('array', $model->instanceArrayPropTest);
        self::assertCount(2, $model->instanceArrayPropTest);
        self::assertEmpty($model->instanceArrayPropTest[0]);
        self::assertEmpty($model->instanceArrayPropTest[1]);

        $model->instanceArrayPropTest = array(
            new TestingClass(),
            new TestingClass(),
            new TestingClass2()
        );
        self::assertInternalType('array', $model->instanceArrayPropTest);
        self::assertCount(3, $model->instanceArrayPropTest);
        self::assertInstanceOf('TestingClasses\TestingClass', $model->instanceArrayPropTest[0]);
        self::assertInstanceOf('TestingClasses\TestingClass', $model->instanceArrayPropTest[1]);
        self::assertEmpty($model->instanceArrayPropTest[2]);
    }
}
