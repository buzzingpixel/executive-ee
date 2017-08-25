# Getting Started with BuzzingPixel DataModel

## Installing

To begin using DataModel, require it into your project with composer:

```shell
composer require buzzingpixel/datamodel
```

## Creating a model

To get started using the DataModel, you create your own model/class which extends the BuzzingPixel DataModel `Model` class, and return an array of attribute definitions with the `defineAttributes` method. Here is a simple example of a model that has an integer property called `myInteger`

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
            'myInteger' => DataType::INT
        );
    }
}
```

Now to use this model, you can simply set the property `myInteger` as you would any other property.

```php
$model = new MyModel();
var_dump($model->myInteger); // This will be null since it has not been set yet
$model->myInteger = 35;
var_dump($model->myInteger); // Now this will be 35
```

You can also set some basic validation rules.

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
            'myInteger' => array(
                'type' => DataType::INT,
                'required' => true,
                'min' => 2,
                'max' => 10
            )
        );
    }
}

$model = new MyModel();
$model->myInteger = 1;
var_dump($model->validate()); // This will be false, also sets the following two properties
var_dump($model->hasErrors); // This will be true
var_dump($model->errors); // An array where the key is the property with errors and the value is an array of the errors for that property
```

You can also set your own validation rules and your own custom data types. See the more detailed documentation for how to do this.

## Collection of models

You can also create a collection of models.

```php
$collection = new \BuzzingPixel\DataModel\ModelCollection(array(
    new MyModel(array(
        'myInteger' => 2
    )),
    new MyModel(array(
        'myInteger' => 3
    ))
);
```

Collections can be iterated over with `foreach` and counted with `count($collection)`. There are also methods for adding a single model, an array of models setting a new array of models, removing a model, emptying the collection, plucking the value of a specific property from each model in the collection, getting an array of models as array, and ordering the models in the collection by a specific property.
