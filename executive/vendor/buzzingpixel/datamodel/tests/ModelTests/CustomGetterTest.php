<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class CustomGetterTest
 * @group modelTests
 */
class CustomGetterTest extends TestCase
{
    /**
     * Test model custom getters
     */
    public function testCustomGetter()
    {
        $model = new ModelInstance();

        self::assertEquals('customGetterTestVal', $model->customGetterTest);

        self::assertEmpty($model->customGetPropTest);

        $model->customGetPropTest = 'test';
        self::assertEquals('customGetPropTestVal', $model->customGetPropTest);

        $model->customGetPropTest = 'testing';
        self::assertEquals('testing', $model->customGetPropTest);
    }
}
