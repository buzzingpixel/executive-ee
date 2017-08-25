<?php

namespace ModelTests\ValidationTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class EmptyModelValidationTest
 * @group modelTests
 */
class AddErrorTest extends TestCase
{
    /**
     * Test
     */
    public function test()
    {
        $model = new ModelInstance();

        $model->setDefinedAttributes(array(
            'test' => array(
                'type' => 'string',
                'required' => true,
            ),
        ));

        $model->addError('test', 'test message');

        self::assertTrue($model->hasErrors);

        self::assertEquals(
            array (
                'test' => array (
                    0 => 'test message',
                ),
            ),
            $model->errors
        );

        self::assertFalse($model->validate());

        self::assertTrue($model->hasErrors);

        $model->addError('test', 'test message 2');

        self::assertTrue($model->hasErrors);

        self::assertEquals(
            array (
                'test' => array (
                    0 => 'This field is required',
                    1 => 'test message 2',
                ),
            ),
            $model->errors
        );
    }
}
