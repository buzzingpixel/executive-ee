<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class EmailArrayPropertyTest
 * @group modelTests
 */
class EmailArrayPropertyTest extends TestCase
{
    /**
     * Test instance model property type
     */
    public function testProperty()
    {
        $model = new ModelInstance();

        $model->emailArrayPropTest = 'test';
        self::assertInternalType('array', $model->emailArrayPropTest);
        self::assertCount(1, $model->emailArrayPropTest);
        self::assertEmpty($model->emailArrayPropTest[0]);

        $model->emailArrayPropTest = 'test|test@gmail.com';
        self::assertInternalType('array', $model->emailArrayPropTest);
        self::assertCount(2, $model->emailArrayPropTest);
        self::assertEmpty($model->emailArrayPropTest[0]);
        self::assertEquals('test@gmail.com', $model->emailArrayPropTest[1]);

        $model->emailArrayPropTest = array('tj@gmail.com', 'tj@', 'person@site.eu');
        self::assertInternalType('array', $model->emailArrayPropTest);
        self::assertCount(3, $model->emailArrayPropTest);
        self::assertEquals('tj@gmail.com', $model->emailArrayPropTest[0]);
        self::assertEmpty($model->emailArrayPropTest[1]);
        self::assertEquals('person@site.eu', $model->emailArrayPropTest[2]);
    }
}
