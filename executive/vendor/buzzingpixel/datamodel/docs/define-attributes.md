# DataModel Define Attributes Method

Defining attributes is the heart and soul of models. Defining attributes determines what is settable/retrievable on the model and what type of data that is.

## `defineAttributes` method

When creating a model class, you can define the attributes of the model from the `defineAttributes` method.

```php
<?php

use BuzzingPixel\DataModel\Model;
use BuzzingPixel\DataModel\DataType;

/**
 * Class MyModel
 * 
 * @property int $myInteger
 */
class MyModel extends Model
{
    /**
     * Define attributes
     *
     * @return array
     */
    public function defineAttributes()
    {
        return array(
            'myInteger' => DataType::INT,
            'myMinMaxRequiredInt' => array(
                'type' => DataType::INT,
                'required' => true,
                'min' => 10,
                'max' => 20
            )
        );
    }
}
```

## `setDefinedAttributes` method

You can also set defined attributes on the model at any time through the method `setDefinedAttributes`.

The first argument is an array of attributes. If an attribute already exists, this will override it.

The second argument is a boolean that defaults to `true` determining whether we should clear previous attributes and set new attributes or whether we should add to or override existing attributes.

```php
$model->setDefinedAttributes(array('myProperty' => DataType::FLOAT), false);
```

## `getDefinedAttributes` method

You can get the model's currently defined attributes at any time using the `getDefinedAttributes` method.
