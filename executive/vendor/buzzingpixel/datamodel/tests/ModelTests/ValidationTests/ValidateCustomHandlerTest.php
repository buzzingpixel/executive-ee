<?php

namespace ModelTests\ValidationTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelCustomHandlers;

/**
 * Class ValidateCustomHandlerTest
 * @group modelTests
 */
class ValidateCustomHandlerTest extends TestCase
{
    /**
     * Test
     */
    public function test()
    {
        $model = new ModelCustomHandlers();
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);

        $model->customDataProp = 'testValidationFail';
        self::assertFalse($model->validate());
        self::assertTrue($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertArrayHasKey('customDataProp', $model->errors);
        self::assertCount(1, $model->errors['customDataProp']);
        self::assertEquals('failed', $model->errors['customDataProp'][0]);

        $model->customDataProp = null;
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);

        $model->customDataProp = 'test';
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);
    }
}
