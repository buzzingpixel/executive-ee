<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class ModelMixedPropertyTest
 * @group modelTests
 */
class MixedPropertyTest extends TestCase
{
    /**
     * Test mixed model property type
     */
    public function testProperty()
    {
        $model = new ModelInstance();

        self::assertNull($model->mixedProp);
        self::assertNull($model->getProperty('mixedProp'));

        $model->mixedProp = 1;
        self::assertInternalType('integer', $model->mixedProp);
        self::assertEquals(1, $model->mixedProp);
        self::assertInternalType('integer', $model->getProperty('mixedProp'));
        self::assertEquals(1, $model->getProperty('mixedProp'));

        $model->setProperty('mixedProp', 2);
        self::assertInternalType('integer', $model->mixedProp);
        self::assertEquals(2, $model->mixedProp);
        self::assertInternalType('integer', $model->getProperty('mixedProp'));
        self::assertEquals(2, $model->getProperty('mixedProp'));

        $model->mixedProp = '1';
        self::assertInternalType('string', $model->mixedProp);
        self::assertEquals('1', $model->mixedProp);
        self::assertInternalType('string', $model->getProperty('mixedProp'));
        self::assertEquals('1', $model->getProperty('mixedProp'));

        $model->setProperty('mixedProp', '2');
        self::assertInternalType('string', $model->mixedProp);
        self::assertEquals('2', $model->mixedProp);
        self::assertInternalType('string', $model->getProperty('mixedProp'));
        self::assertEquals('2', $model->getProperty('mixedProp'));
    }
}
