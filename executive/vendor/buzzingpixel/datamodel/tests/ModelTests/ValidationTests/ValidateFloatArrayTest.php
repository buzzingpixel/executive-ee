<?php

namespace ModelTests\ValidationTests;

use BuzzingPixel\DataModel\DataType;
use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class ValidateFloatArrayTest
 * @group modelTests
 */
class ValidateFloatArrayTest extends TestCase
{
    /**
     * Test
     */
    public function test()
    {
        $model = new ModelInstance();

        $model->setDefinedAttributes(array(
            'mixedProp' => array(
                'type' => DataType::FLOAT_ARRAY,
                'required' => true,
                'min' => 3.5,
                'max' => 10.2
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

        $model->mixedProp = array(4, 3.4, 8);
        self::assertFalse($model->validate());
        self::assertTrue($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertArrayHasKey('mixedProp', $model->errors);
        self::assertCount(1, $model->errors['mixedProp']);
        self::assertEquals(
            'This field must not be less than 3.5',
            $model->errors['mixedProp'][0]
        );

        $model->mixedProp = array(4, 10.3, 8);
        self::assertFalse($model->validate());
        self::assertTrue($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertArrayHasKey('mixedProp', $model->errors);
        self::assertCount(1, $model->errors['mixedProp']);
        self::assertEquals(
            'This field must not be more than 10.2',
            $model->errors['mixedProp'][0]
        );

        $model->mixedProp = array(4, 3.5, 8);
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);

        $model->mixedProp = 3.6;
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);

        $model->mixedProp = 10.2;
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);

        $model->mixedProp = 10.1;
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);
    }
}
