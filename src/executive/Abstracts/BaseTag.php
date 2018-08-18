<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\Abstracts;

use BuzzingPixel\Executive\BaseComponent;
use BuzzingPixel\DataModel\DataType;

/**
 * Class BaseTag
 * @property \EE_Template $templateService
 */
abstract class BaseTag extends BaseComponent
{
    /**
     * Define attributes
     */
    public function defineAttributes()
    {
        return array(
            'templateService' => array(
                'type' => DataType::INSTANCE,
                'expect' => '\EE_Template',
            ),
        );
    }
}
