<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class DateTimePropertyTest
 * @group modelTests
 */
class DateTimePropertyTest extends TestCase
{
    /**
     * Test boolean model property type
     */
    public function testProperty()
    {
        $model = new ModelInstance();

        self::assertNull($model->datetimeTest);

        $model->datetimeTest = 1501998027;
        self::assertEquals('1501998027', $model->datetimeTest->getTimestamp());

        $model->datetimeTest = '1501998027';
        self::assertEquals(1501998027, $model->datetimeTest->getTimestamp());

        $model->datetimeTest = '';
        self::assertEquals(time(), $model->datetimeTest->getTimestamp());

        $yesterday = new \DateTime('yesterday');
        $model->datetimeTest = 'yesterday';
        self::assertEquals(
            $yesterday->getTimestamp(),
            $model->datetimeTest->getTimestamp()
        );

        $test = new \DateTime('2017-06-20 16:06:02');
        $model->datetimeTest = '2017-06-20 16:06:02';
        self::assertEquals(
            $test->getTimestamp(),
            $model->datetimeTest->getTimestamp()
        );

        $test = new \DateTime('2010-01-01 00:00:00');
        $model->datetimeTest = $test;
        self::assertEquals(
            $test->getTimestamp(),
            $model->datetimeTest->getTimestamp()
        );
    }
}
