<?php

namespace ModelTests\ValidationTests;

use BuzzingPixel\DataModel\DataType;
use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class ValidateIntTest
 * @group modelTests
 */
class ValidateIntTest extends TestCase
{
    /**
     * Test
     */
    public function test()
    {
        $model = new ModelInstance();

        $model->setDefinedAttributes(array(
            'mixedProp' => array(
                'type' => DataType::INT,
                'required' => true,
                'min' => 3,
                'max' => 10
            )
        ), true);

        self::assertFalse($model->validate());
        self::assertTrue($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertArrayHasKey('mixedProp', $model->errors);
        self::assertCount(1, $model->errors['mixedProp']);
        self::assertEquals(
            'This field is required',
            $model->errors['mixedProp'][0]
        );

        $model->mixedProp = 2;
        self::assertFalse($model->validate());
        self::assertTrue($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertArrayHasKey('mixedProp', $model->errors);
        self::assertCount(1, $model->errors['mixedProp']);
        self::assertEquals(
            'This field must not be less than 3',
            $model->errors['mixedProp'][0]
        );

        $model->mixedProp = 11;
        self::assertFalse($model->validate());
        self::assertTrue($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertArrayHasKey('mixedProp', $model->errors);
        self::assertCount(1, $model->errors['mixedProp']);
        self::assertEquals(
            'This field must not be more than 10',
            $model->errors['mixedProp'][0]
        );

        $model->mixedProp = 3;
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);

        $model->mixedProp = 4;
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);

        $model->mixedProp = 9;
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);

        $model->mixedProp = 10;
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);
    }
}
