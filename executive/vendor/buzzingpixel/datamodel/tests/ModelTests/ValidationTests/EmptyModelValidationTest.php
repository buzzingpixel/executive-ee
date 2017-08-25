<?php

namespace ModelTests\ValidationTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class EmptyModelValidationTest
 * @group modelTests
 */
class EmptyModelValidationTest extends TestCase
{
    /**
     * Test
     */
    public function test()
    {
        $model = new ModelInstance();

        self::assertTrue($model->validate());
    }
}
