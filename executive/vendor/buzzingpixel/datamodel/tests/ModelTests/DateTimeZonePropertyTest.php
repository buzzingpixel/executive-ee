<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class DateTimeZonePropertyTest
 * @group modelTests
 */
class DateTimeZonePropertyTest extends TestCase
{
    /**
     * Test boolean model property type
     */
    public function testProperty()
    {
        $model = new ModelInstance();

        self::assertNull($model->dateTimezoneTest);

        $model->dateTimezoneTest = 'America/Chicago';
        self::assertEquals(
            'America/Chicago',
            $model->dateTimezoneTest->getName()
        );

        $model->dateTimezoneTest = 'America/New_York';
        self::assertEquals(
            'America/New_York',
            $model->dateTimezoneTest->getName()
        );

        $model->dateTimezoneTest = 'asdf';
        self::assertNull($model->dateTimezoneTest);
    }
}
