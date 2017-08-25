# BuzzingPixel DataModel ModelCollection Class Reference

## Constructor

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

- Argument 1: (array) Optional array of models to populate the collection with

## Iterator

`ModelCollection` implements PHPs `\Iterator` interface so you can do iterate over models in the collection with foreach like so:

```php
foreach ($modelCollection as $model) {
    var_dump($model->uuid);
}
```

## Countable

`ModelCollection` implements PHPs `\Countable` interface so you can get the count of models in the collection like so:

```php
$numOfModels = count($modelCollection);
```

## Methods

### `$modelCollection->count()`

- Return: (int) The count of models in the collection

### `$modelCollection->addModel((\BuzzingPixel\DataModel\Model) $model)`

- Argument 1: `(\BuzzingPixel\DataModel\Model)` A model instance to add to the collection
- Return: `(self)` An instance of the current collection

### `$modelCollection->addModels((array) $models)`

- Argument 1: `(array)` An array of `\BuzzingPixel\DataModel\Model` to add to the collection
- Return: `(self)` An instance of the current collection

### `$modelCollection->setModels((array) $models)`

- Argument 1: `(array)` An array of `\BuzzingPixel\DataModel\Model` to set to the collection
- Return: `(self)` An instance of the current collection

This method clears all existing models before setting the models from the argument.

### `$modelCollection->setModels((array) $models)`

- Return: `(self)` An instance of the current collection

Empties all models from the collection.

### `$modelCollection->removeModel((int|string|Model) $model)`

- Argument 1: `(int|string|\BuzzingPixel\DataModel\Model)`
    - `(int)` Removes the model at that index
    - `(string)` Removes a model matching the passed in UUID
    - `(\BuzzingPixel\DataModel\Model)` Removes the model matching that instance
- Return: `(self)` An instance of the current collection

### `$modelCollection->pluck((string) $property)`

- Argument 1: `(string)` The property to pluck from the models
- Return: `(array)` An array of the values of the specified properties from each model

### `$modelCollection->asArray((string) $property = 'uuid')`

- Argument 1: `(string)` Optional. The property to use as the keys for the array
    - Default: `uuid`;
- Return: `(array)` The array values of the models

This method runs `asArray` on each of the models in the collection and return an array of those arrays.

### `$modelCollection->orderBy((string) $prop, (string) $dir = 'asc')`

- Argument 1: `(string)` Specify the property to order the models by
- Argument 2: `(string)` Specify the direction
    - Default: `asc`
    - Acceptable values: `asc` or `desc`
- Return: `(self)` An instance of the current collection
