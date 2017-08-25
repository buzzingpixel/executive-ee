<?php

namespace ModelTests;

use PHPUnit\Framework\TestCase;
use TestingClasses\ModelInstance;

/**
 * Class CloneModelTest
 * @group modelTests
 */
class CloneModelTest extends TestCase
{
    /**
     * Test boolean model property type
     */
    public function testProperty()
    {
        $model = new ModelInstance();
        $clonedModel = clone $model;

        self::assertNotEquals($model->uuid, $clonedModel->uuid);
    }
}
