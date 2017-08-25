<?php

namespace ModelTests\ValidationTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelCustomHandlers;

/**
 * Class ValidateCustomMethodTest
 * @group modelTests
 */
class ValidateCustomMethodTest extends TestCase
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

        $model->customDataProp2 = 'testValidationFail';
        self::assertFalse($model->validate());
        self::assertTrue($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertArrayHasKey('customDataProp2', $model->errors);
        self::assertCount(1, $model->errors['customDataProp2']);
        self::assertEquals('hasFailed', $model->errors['customDataProp2'][0]);

        $model->customDataProp2 = null;
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);

        $model->customDataProp2 = 'test';
        self::assertTrue($model->validate());
        self::assertFalse($model->hasErrors);
        self::assertInternalType('array', $model->errors);
        self::assertCount(0, $model->errors);
    }
}
