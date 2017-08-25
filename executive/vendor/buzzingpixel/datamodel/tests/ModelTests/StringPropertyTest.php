<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class StringPropertyTest
 * @group modelTests
 */
class StringPropertyTest extends TestCase
{
    /**
     * Test string model property type
     */
    public function testProperty()
    {
        $model = new ModelInstance();

        self::assertNull($model->stringProp);
        self::assertNull($model->getProperty('stringProp'));

        $model->stringProp = 1;
        self::assertInternalType('string', $model->stringProp);
        self::assertEquals('1', $model->stringProp);
        self::assertInternalType('string', $model->getProperty('stringProp'));
        self::assertEquals('1', $model->getProperty('stringProp'));

        $model->stringProp = 1.2;
        self::assertInternalType('string', $model->stringProp);
        self::assertEquals('1.2', $model->stringProp);
        self::assertInternalType('string', $model->getProperty('stringProp'));
        self::assertEquals('1.2', $model->getProperty('stringProp'));

        $model->stringProp = 'asdf';
        self::assertInternalType('string', $model->stringProp);
        self::assertEquals('asdf', $model->stringProp);
        self::assertInternalType('string', $model->getProperty('stringProp'));
        self::assertEquals('asdf', $model->getProperty('stringProp'));
    }
}
