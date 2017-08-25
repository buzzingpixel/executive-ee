<?php

namespace TestingClasses;

use BuzzingPixel\DataModel\Model;

/**
 * Class ModelCustomHandlers
 *
 * @property mixed $customDataProp
 * @property mixed $customDataProp2
 */
class ModelCustomHandlers extends Model
{
    const CUSTOM_HANDLERS = array(
        'CustomDataType' => '\CustomHandlers\CustomDataTypeHandler'
    );

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * Define attributes
     *
     * @return array
     */
    public function defineAttributes()
    {
        return array(
            'customDataProp' => 'CustomDataType',
            'customDataProp2' => 'CustomDataType'
        );
    }

    /**
     * Validate customDataProp2
     * @param mixed $val
     * @return array
     */
    protected function validateCustomDataProp2($val)
    {
        if ($val === 'testValidationFail') {
            return array('hasFailed');
        }

        return array();
    }
}
