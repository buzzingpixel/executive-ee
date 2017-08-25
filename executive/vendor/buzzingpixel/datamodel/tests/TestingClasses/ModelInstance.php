<?php

namespace TestingClasses;

use BuzzingPixel\DataModel\Model;
use BuzzingPixel\DataModel\DataType;
use BuzzingPixel\DataModel\ModelCollection;

/**
 * Class ModelInstance
 *
 * @property mixed $mixedProp
 * @property string $stringProp
 * @property int $intProp
 * @property int $intPropExtra
 * @property float $floatProp
 * @property bool $boolProp
 * @property bool $instanceProp
 * @property mixed $enumProp
 * @property string $emailProp
 * @property-write string $customSetterProperty
 * @property int $customSetTest
 * @property-read string $customGetterTest
 * @property string $customGetPropTest
 * @property array $arrayPropTest
 * @property int[] $intArrayPropTest
 * @property string[] $stringArrayPropTest
 * @property float[] $floatArrayPropTest
 * @property string[] $emailArrayPropTest
 * @property TestingClass[] $instanceArrayPropTest
 * @property ModelCollection $collectionPropTest
 * @property \DateTime $datetimeTest
 * @property \DateTime $datetimeTestRequired
 * @property \DateTimeZone $dateTimezoneTest
 */
class ModelInstance extends Model
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * Define attributes
     *
     * @return array
     */
    public function defineAttributes()
    {
        return array(
            'mixedProp' => DataType::MIXED,
            'stringProp' => DataType::STRING,
            'intProp' => DataType::INT,
            'intPropExtra' => array(
                'type' => DataType::INT,
            ),
            'floatProp' => DataType::FLOAT,
            'boolProp' => DataType::BOOL,
            'instanceProp' => array(
                'type' => DataType::INSTANCE,
                'expect' => '\TestingClasses\TestingClass',
            ),
            'enumProp' => array(
                'type' => DataType::ENUM,
                'expect' => array(
                    123,
                    'someVal',
                    1.2,
                )
            ),
            'emailProp' => DataType::EMAIL,
            'customSetTest' => DataType::MIXED,
            'customGetPropTest' => DataType::MIXED,
            'arrayPropTest' => DataType::ARR,
            'intArrayPropTest' => DataType::INT_ARRAY,
            'stringArrayPropTest' => DataType::STRING_ARRAY,
            'floatArrayPropTest' => DataType::FLOAT_ARRAY,
            'emailArrayPropTest' => DataType::EMAIL_ARRAY,
            'instanceArrayPropTest' => array(
                'type' => DataType::INSTANCE_ARRAY,
                'expect' => '\TestingClasses\TestingClass',
            ),
            'collectionPropTest' => DataType::COLLECTION,
            'datetimeTest' => DataType::DATETIME,
            'dateTimezoneTest' => DataType::DATE_TIMEZONE,
        );
    }

    /** @var string $testCustomSetterProperty */
    public $testCustomSetterProperty = '';

    /**
     * Set customSetterProperty
     *
     * @param mixed $val
     */
    protected function setCustomSetterProperty($val)
    {
        $this->testCustomSetterProperty = $val;
    }

    /**
     * Set customSetTest
     *
     * @param mixed $val
     * @return int
     */
    protected function setCustomSetTest($val)
    {
        return (int)$val;
    }

    /**
     * Get customGetterTest
     *
     * @return string
     */
    protected function getCustomGetterTest()
    {
        return 'customGetterTestVal';
    }

    /**
     * Get customGetPropTest
     *
     * @param mixed $existingVal
     * @return mixed
     */
    protected function getCustomGetPropTest($existingVal)
    {
        if ($existingVal === 'test') {
            return 'customGetPropTestVal';
        }

        return $existingVal;
    }
}
