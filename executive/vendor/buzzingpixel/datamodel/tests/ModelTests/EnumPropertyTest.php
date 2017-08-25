<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;
use TestingClasses\TestingClass;
use TestingClasses\TestingClass2;

/**
 * Class EnumPropertyTest
 * @group modelTests
 */
class EnumPropertyTest extends TestCase
{
    /**
     * Test enum model property type
     */
    public function testProperty()
    {
        $model = new ModelInstance();

        self::assertNull($model->enumProp);

        $model->enumProp = 'asdf';
        self::assertNull($model->enumProp);

        $model->enumProp = 123;
        self::assertInternalType('integer', $model->enumProp);
        self::assertEquals(123, $model->enumProp);

        $model->enumProp = 'asdf';
        self::assertNull($model->enumProp);

        $model->enumProp = 'someVal';
        self::assertInternalType('string', $model->enumProp);
        self::assertEquals('someVal', $model->enumProp);

        $model->enumProp = 1.2;
        self::assertInternalType('float', $model->enumProp);
        self::assertEquals(1.2, $model->enumProp);

        $model->enumProp = 1.3;
        self::assertNull($model->enumProp);
    }
}
