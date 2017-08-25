<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;
use TestingClasses\TestingClass;
use TestingClasses\TestingClass2;

/**
 * Class InstancePropertyTest
 * @group modelTests
 */
class InstancePropertyTest extends TestCase
{
    /**
     * Test instance model property type
     */
    public function testProperty()
    {
        $model = new ModelInstance();

        self::assertNull($model->instanceProp);

        $model->instanceProp = 'asdf';
        self::assertNull($model->instanceProp);

        $model->instanceProp = new TestingClass2();
        self::assertNull($model->instanceProp);

        $model->instanceProp = new TestingClass();
        self::assertInstanceOf('\TestingClasses\TestingClass', $model->instanceProp);
    }
}
