<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class IntPropertyTest
 * @group modelTests
 */
class IntPropertyTest extends TestCase
{
    /**
     * Test integer model property type
     */
    public function testProperty()
    {
        $model = new ModelInstance();

        self::assertNull($model->intProp);
        self::assertNull($model->getProperty('intProp'));
        self::assertNull($model->intPropExtra);
        self::assertNull($model->getProperty('intPropExtra'));

        $model->intProp = 1;
        $model->intPropExtra = 1;
        self::assertInternalType('integer', $model->intProp);
        self::assertEquals(1, $model->intProp);
        self::assertInternalType('integer', $model->intPropExtra);
        self::assertEquals(1, $model->intPropExtra);
        self::assertInternalType('integer', $model->getProperty('intProp'));
        self::assertEquals(1, $model->getProperty('intProp'));
        self::assertInternalType('integer', $model->getProperty('intPropExtra'));
        self::assertEquals(1, $model->getProperty('intPropExtra'));

        $model->intProp = 1.2;
        $model->intPropExtra = 1.2;
        self::assertInternalType('integer', $model->intProp);
        self::assertEquals(1, $model->intProp);
        self::assertInternalType('integer', $model->intPropExtra);
        self::assertEquals(1, $model->intPropExtra);
        self::assertInternalType('integer', $model->getProperty('intProp'));
        self::assertEquals(1, $model->getProperty('intProp'));
        self::assertInternalType('integer', $model->getProperty('intPropExtra'));
        self::assertEquals(1, $model->getProperty('intPropExtra'));

        $model->intProp = 'asdf';
        $model->intPropExtra = 'asdf';
        self::assertInternalType('integer', $model->intProp);
        self::assertEquals(0, $model->intProp);
        self::assertInternalType('integer', $model->intPropExtra);
        self::assertEquals(0, $model->intPropExtra);
        self::assertInternalType('integer', $model->getProperty('intProp'));
        self::assertEquals(0, $model->getProperty('intProp'));
        self::assertInternalType('integer', $model->getProperty('intPropExtra'));
        self::assertEquals(0, $model->getProperty('intPropExtra'));
    }
}
