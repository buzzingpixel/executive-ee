<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class DirtyValueTest
 * @group modelTests
 */
class DirtyValueTest extends TestCase
{
    /**
     * Test
     */
    public function test()
    {
        $model = new ModelInstance();

        $model->intProp = 'test';
        self::assertInternalType('integer', $model->intProp);
        self::assertEquals(0, $model->intProp);
        self::assertInternalType('string', $model->getDirtyValue('intProp'));
        self::assertEquals('test', $model->getDirtyValue('intProp'));

        $model->intProp = '2';
        self::assertInternalType('integer', $model->intProp);
        self::assertEquals(2, $model->intProp);
        self::assertInternalType('string', $model->getDirtyValue('intProp'));
        self::assertEquals('2', $model->getDirtyValue('intProp'));

        $model->intProp = 3;
        self::assertInternalType('integer', $model->intProp);
        self::assertEquals(3, $model->intProp);
        self::assertInternalType('integer', $model->getDirtyValue('intProp'));
        self::assertEquals(3, $model->getDirtyValue('intProp'));
    }
}
