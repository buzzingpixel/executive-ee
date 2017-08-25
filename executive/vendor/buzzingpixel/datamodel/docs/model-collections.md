# DataModel Collections

A model collection is a container class for models with methods to perform operations on those models or sort them.

## Creating a model collection

When you create a new collection, you can optionally pass an array of models into the constructor. Or you can always set models later using the `setModels` method.

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
