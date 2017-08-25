# DataModel Custom Data Handlers

Sometimes you might need to create your own custom data handlers. To define a custom data handler, add a constant `CUSTOM_HANDLERS` to your model like this:

```php
<?php

class MyModel extends \BuzzingPixel\DataModel\Model
{
    const CUSTOM_HANDLERS = array(
        'CustomDataType' => '\CustomHandlers\CustomDataType',
        'OtherCustomDataType' => '\CustomHandlers\OtherCustomDataType'
    );

    public function defineAttributes()
    {
        return array(
            'myCustomProp' => array(
                'type' => 'CustomDataType',
                'customDef' => 'customVal'
            ),
            'myOtherCustomProp' => 'OtherCustomDataType'
        );
    }
}
```

These custom data handler classes can have 4 methods, all optional. If the method is defined, it will be used for get/set/asArray/validate.

You define what method to use for each of the 4 operations on a class constant. This is so that if the same method can be used for more than one thing (many of the internal data types use one method for get, set, and asArray), then you just map to the same method on the class with the constant.

## Full class example

This example uses a common method shared between get and set, and separate methods for asArray and validation:

```php
<?php

class CustomDataType
{
    /** @var string GET_HANDLER */
    const GET_HANDLER = 'commonHandler';

    /** @var string SET_HANDLER */
    const SET_HANDLER = 'commonHandler';

    /** @var string AS_ARRAY_HANDLER */
    const AS_ARRAY_HANDLER = 'asArrayHandler';

    /** @var string VALIDATION_HANDLER */
    const VALIDATION_HANDLER = 'validationHandler';

    /**
     * Common method to handle data
     * @param mixed $val
     * @return string
     */
    public function commonHandler($val)
    {
        // Do custom stuff to the data here

        // Return the val here
        return $val;
    }

    /**
     * Common method to handle data
     * @param mixed $val
     * @return string
     */
    public function asArrayHandler($val)
    {
        // Do custom stuff to the data here
        
        // Return the val here
        return $val;
    }

    /**
     * Validation handler
     * @param mixed $val
     * @return array
     */
    public function validationHandler($val)
    {
        if ($val === 'someValue') {
            return array('failed');
        }

        return array();
    }
}
```
