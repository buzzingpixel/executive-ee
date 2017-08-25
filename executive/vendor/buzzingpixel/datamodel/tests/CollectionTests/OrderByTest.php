<?php

namespace CollectionTests;

use PHPUnit\Framework\TestCase;

use BuzzingPixel\DataModel\ModelCollection;
use TestingClasses\ModelInstance;

/**
 * Class OrderByTest
 * @group collectionTests
 */
class OrderByTest extends TestCase
{
    /**
     * Test iterator
     */
    public function test()
    {
        $model1 = new ModelInstance(array(
            'stringProp' => 'abc',
            'intProp' => 2
        ));
        $model2 = new ModelInstance(array(
            'stringProp' => 'def',
            'intProp' => 1
        ));
        $items = array($model1, $model2);
        $collection = new ModelCollection($items);

        $collection->orderBy('stringProp', 'desc');
        $i = 0;
        foreach ($collection as $model) {
            if ($i === 0) {
                self::assertEquals($model2->uuid, $model->uuid);
            } elseif ($i === 1) {
                self::assertEquals($model1->uuid, $model->uuid);
            }

            $i++;
        }

        $collection->orderBy('stringProp', 'asc');
        $i = 0;
        foreach ($collection as $model) {
            if ($i === 0) {
                self::assertEquals($model1->uuid, $model->uuid);
            } elseif ($i === 1) {
                self::assertEquals($model2->uuid, $model->uuid);
            }

            $i++;
        }

        $collection->orderBy('stringProp');
        $i = 0;
        foreach ($collection as $model) {
            if ($i === 0) {
                self::assertEquals($model1->uuid, $model->uuid);
            } elseif ($i === 1) {
                self::assertEquals($model2->uuid, $model->uuid);
            }

            $i++;
        }

        $collection->orderBy('intProp');
        $i = 0;
        foreach ($collection as $model) {
            if ($i === 0) {
                self::assertEquals($model2->uuid, $model->uuid);
            } elseif ($i === 1) {
                self::assertEquals($model1->uuid, $model->uuid);
            }

            $i++;
        }

        $collection->orderBy('intProp', 'desc');
        $i = 0;
        foreach ($collection as $model) {
            if ($i === 0) {
                self::assertEquals($model1->uuid, $model->uuid);
            } elseif ($i === 1) {
                self::assertEquals($model2->uuid, $model->uuid);
            }

            $i++;
        }
    }
}
