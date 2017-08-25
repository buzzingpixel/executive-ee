<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class BoolPropertyTest
 * @group modelTests
 */
class BoolPropertyTest extends TestCase
{
    /**
     * Test boolean model property type
     */
    public function testProperty()
    {
        $model = new ModelInstance();

        self::assertNull($model->boolProp);
        self::assertNull($model->getProperty('boolProp'));

        $model->boolProp = '1';
        self::assertInternalType('boolean', $model->boolProp);
        self::assertTrue($model->boolProp);

        $model->boolProp = 1;
        self::assertInternalType('boolean', $model->boolProp);
        self::assertTrue($model->boolProp);

        $model->boolProp = 'true';
        self::assertInternalType('boolean', $model->boolProp);
        self::assertTrue($model->boolProp);

        $model->boolProp = true;
        self::assertInternalType('boolean', $model->boolProp);
        self::assertTrue($model->boolProp);

        $model->boolProp = 'y';
        self::assertInternalType('boolean', $model->boolProp);
        self::assertTrue($model->boolProp);

        $model->boolProp = 'yes';
        self::assertInternalType('boolean', $model->boolProp);
        self::assertTrue($model->boolProp);

        $model->boolProp = '0';
        self::assertInternalType('boolean', $model->boolProp);
        self::assertFalse($model->boolProp);

        $model->boolProp = 0;
        self::assertInternalType('boolean', $model->boolProp);
        self::assertFalse($model->boolProp);

        $model->boolProp = 'false';
        self::assertInternalType('boolean', $model->boolProp);
        self::assertFalse($model->boolProp);

        $model->boolProp = false;
        self::assertInternalType('boolean', $model->boolProp);
        self::assertFalse($model->boolProp);

        $model->boolProp = 'n';
        self::assertInternalType('boolean', $model->boolProp);
        self::assertFalse($model->boolProp);

        $model->boolProp = 'no';
        self::assertInternalType('boolean', $model->boolProp);
        self::assertFalse($model->boolProp);

        $model->boolProp = 'asdf';
        self::assertInternalType('boolean', $model->boolProp);
        self::assertFalse($model->boolProp);
    }
}
