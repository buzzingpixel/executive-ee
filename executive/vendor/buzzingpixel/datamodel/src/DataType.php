<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\DataModel;

/**
 * Class DataType
 */
class DataType
{
    /** @var string ARR */
    const ARR = 'array';

    /** @var string BOOL */
    const BOOL = 'bool';

    /** @var string COLLECTION */
    const COLLECTION = 'collection';

    /** @var string EMAIL */
    const EMAIL = 'email';

    /** @var string EMAIL_ARRAY */
    const EMAIL_ARRAY = 'emailArray';

    /** @var string ENUM */
    const ENUM = 'enum';

    /** @var string FLOAT */
    const FLOAT = 'float';

    /** @var string FLOAT_ARRAY */
    const FLOAT_ARRAY = 'floatArray';

    /** @var string INSTANCE */
    const INSTANCE = 'instance';

    /** @var string INSTANCE_ARRAY */
    const INSTANCE_ARRAY = 'instanceArray';

    /** @var string INT */
    const INT = 'int';

    /** @var string INT_ARRAY */
    const INT_ARRAY = 'intArray';

    /** @var string MIXED */
    const MIXED = 'mixed';

    /** @var string STRING */
    const STRING = 'string';

    /** @var string STRING_ARRAY */
    const STRING_ARRAY = 'stringArray';

    /** @var string DATETIME */
    const DATETIME = 'datetime';

    /** @var string DATE_TIMEZONE */
    const DATE_TIMEZONE = 'dateTimezone';
}
