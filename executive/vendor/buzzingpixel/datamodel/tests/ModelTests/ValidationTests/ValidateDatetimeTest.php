<?php

namespace ModelTests\ValidationTests;

use BuzzingPixel\DataModel\DataType;
use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class ValidateDatetimeTest
 * @group modelTests
 */
class ValidateDatetimeTest extends TestCase
{
    /**
     * Test
     */
    public function test()
    {
        $model = new ModelInstance();

        $model->setDefinedAttributes(array(
            'datetimeTest' => array(
                'type' => DataType::DATETIME,
                'required' => true,
            )
        ), true);

        self::assertFalse($model->validate());
        self::assertTrue($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertArrayHasKey('datetimeTest', $model->errors);
        self::assertCount(1, $model->errors['datetimeTest']);
        self::assertEquals(
            'This field is required',
            $model->errors['datetimeTest'][0]
        );

        $model->datetimeTest = 'asdf';
        self::assertFalse($model->validate());
        self::assertTrue($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertArrayHasKey('datetimeTest', $model->errors);
        self::assertCount(1, $model->errors['datetimeTest']);
        self::assertEquals(
            'This field is required',
            $model->errors['datetimeTest'][0]
        );

        $model->datetimeTest = 'today';
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);

        $model->datetimeTest = 'asdf';
        self::assertFalse($model->validate());
        self::assertTrue($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertArrayHasKey('datetimeTest', $model->errors);
        self::assertCount(1, $model->errors['datetimeTest']);
        self::assertEquals(
            'This field is required',
            $model->errors['datetimeTest'][0]
        );
    }
}
