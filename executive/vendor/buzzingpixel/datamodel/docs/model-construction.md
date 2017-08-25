# DataModel Construction

On construction of a model class, you can send an array of key => value pairs to populate the model with. The keys must be set already from the model's `defineAttributes` method.

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

$model = new MyModel(array(
    'myInteger' => 3,
    'someProperty' => 'someValue'
));

var_dump($model->myInteger); // Dumped value of 3
var_dump($model->someProperty); // Throws an error because `someProperty` is not a defined attribute and so was not set
```
