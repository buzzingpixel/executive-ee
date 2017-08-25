<?php

namespace ModelTests\ValidationTests;

use BuzzingPixel\DataModel\DataType;
use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class ValidateDateTimezoneTest
 * @group modelTests
 */
class ValidateDateTimezoneTest extends TestCase
{
    /**
     * Test
     */
    public function test()
    {
        $model = new ModelInstance();

        $model->setDefinedAttributes(array(
            'dateTimezoneTest' => array(
                'type' => DataType::DATE_TIMEZONE,
                'required' => true,
            )
        ), true);

        self::assertFalse($model->validate());
        self::assertTrue($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertArrayHasKey('dateTimezoneTest', $model->errors);
        self::assertCount(1, $model->errors['dateTimezoneTest']);
        self::assertEquals(
            'This field is required',
            $model->errors['dateTimezoneTest'][0]
        );

        $model->dateTimezoneTest = 'asdf';
        self::assertFalse($model->validate());
        self::assertTrue($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertArrayHasKey('dateTimezoneTest', $model->errors);
        self::assertCount(1, $model->errors['dateTimezoneTest']);
        self::assertEquals(
            'This field is required',
            $model->errors['dateTimezoneTest'][0]
        );

        $model->dateTimezoneTest = 'Europe/London';
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);

        $model->dateTimezoneTest = 'asdf';
        self::assertFalse($model->validate());
        self::assertTrue($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertArrayHasKey('dateTimezoneTest', $model->errors);
        self::assertCount(1, $model->errors['dateTimezoneTest']);
        self::assertEquals(
            'This field is required',
            $model->errors['dateTimezoneTest'][0]
        );
    }
}
