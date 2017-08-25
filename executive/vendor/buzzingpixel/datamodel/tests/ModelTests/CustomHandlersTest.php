<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelCustomHandlers;

/**
 * Class CustomHandlersTest
 * @group modelTests
 */
class CustomHandlersTest extends TestCase
{
    /**
     * Test model custom getters
     */
    public function test()
    {
        $model = new ModelCustomHandlers(array(
            'customDataProp' => 'testing1'
        ));

        self::assertEquals('testing1', $model->customDataProp);

        $model->customDataProp = 'testGet';
        self::assertEquals('customGetTestVal', $model->customDataProp);

        $model->customDataProp = 'testSet';
        self::assertEquals('customSetTestVal', $model->customDataProp);

        $model->customDataProp = 'testAsArray';
        $asArray = $model->asArray();
        self::assertInternalType('array', $asArray);
        self::assertEquals('customAsArrayTestVal', $asArray['customDataProp']);
    }
}
