<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class CustomSetterTest
 * @group modelTests
 */
class CustomSetterTest extends TestCase
{
    /**
     * Test model custom setters
     */
    public function testCustomSetter()
    {
        $model = new ModelInstance();

        self::assertEmpty($model->testCustomSetterProperty);

        $model->customSetterProperty = 'asdf';
        self::assertEquals('asdf', $model->testCustomSetterProperty);

        $model->customSetTest = 'asdf';
        self::assertInternalType('integer', $model->customSetTest);
        self::assertEquals(0, $model->customSetTest);

        $model->customSetTest = '3';
        self::assertInternalType('integer', $model->customSetTest);
        self::assertEquals(3, $model->customSetTest);
    }
}
