<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class StringArrayPropertyTest
 * @group modelTests
 */
class StringArrayPropertyTest extends TestCase
{
    /**
     * Test model property
     */
    public function testProperty()
    {
        $model = new ModelInstance();

        $model->stringArrayPropTest = 'test';
        self::assertInternalType('array', $model->stringArrayPropTest);
        self::assertCount(1, $model->stringArrayPropTest);
        self::assertEquals('test', $model->stringArrayPropTest[0]);

        $model->stringArrayPropTest = 'test|test2';
        self::assertInternalType('array', $model->stringArrayPropTest);
        self::assertCount(2, $model->stringArrayPropTest);
        self::assertInternalType('string', $model->stringArrayPropTest[0]);
        self::assertEquals('test', $model->stringArrayPropTest[0]);
        self::assertInternalType('string', $model->stringArrayPropTest[1]);
        self::assertEquals('test2', $model->stringArrayPropTest[1]);

        $model->stringArrayPropTest = '12|test|145asdf';
        self::assertInternalType('array', $model->stringArrayPropTest);
        self::assertCount(3, $model->stringArrayPropTest);
        self::assertInternalType('string', $model->stringArrayPropTest[0]);
        self::assertEquals('12', $model->stringArrayPropTest[0]);
        self::assertInternalType('string', $model->stringArrayPropTest[1]);
        self::assertEquals('test', $model->stringArrayPropTest[1]);
        self::assertInternalType('string', $model->stringArrayPropTest[2]);
        self::assertEquals('145asdf', $model->stringArrayPropTest[2]);

        $model->stringArrayPropTest = array('12345', '14asdf56', 'testing');
        self::assertInternalType('array', $model->stringArrayPropTest);
        self::assertCount(3, $model->stringArrayPropTest);
        self::assertInternalType('string', $model->stringArrayPropTest[0]);
        self::assertEquals(12345, $model->stringArrayPropTest[0]);
        self::assertInternalType('string', $model->stringArrayPropTest[1]);
        self::assertEquals('14asdf56', $model->stringArrayPropTest[1]);
        self::assertInternalType('string', $model->stringArrayPropTest[2]);
        self::assertEquals('testing', $model->stringArrayPropTest[2]);
    }
}
