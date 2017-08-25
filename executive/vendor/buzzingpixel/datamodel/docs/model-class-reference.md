# BuzzingPixel DataModel Class Reference

## Constructor

```php
$model = new \BuzzingPixel\DataModel\Model(array(
    'myProp' => 'myVal',
    'myOtherProp' => 'myOtherVal'
));
```

- Argument 1: (array) Optional array of `$key => $value`s to populate the newly created model with.

## Default Properties

### `$model->uuid`

- Return: `(string)`
- Read only property of the model's unique ID
- Alias of `$model->getUuid()` method

### `$model->hasErrrors`

- Return: `(bool)`
- Read only property of whether the last time the `validate` method was run the model had errors
- Alias of `$model->getHasErrors()`

### `$model->errors`

- Return: `(array)`
- Read only property of any errors on the model from the last time the `validate` method was run
- The keys in this array are the property names that have errors
- The values in the array are an array of messages from the errors
- Alias of `$model->getErrors()`

## Default Methods

### `$model->defineAttributes()`

- Purpose: When extending the model class, use this method to return an array of the attributes for the model.
    - The keys in the returned array should be the attribute names
    - The values in the array should be the attribute definition
    - This function is called from the constructor and does nothing to the model after model construction
    
### `$model->setDefinedAttributes((array) $attrs, (bool) $clearPrevious)`

- Argument 1: `(array)` Array of attributes to set on the model (see defineAttributes)
- Argument 2: `(bool)` Whether to clear previous attributes before setting new attributes
- Return: `(self)` An instance of the current model

This method can be run at any time to modify the model.

### `$model->getDefinedAttributes()`

- Return: `(array)` An array of the currently defined attributes

### `$model->setProperty((string) $name, (mixed) $value)`

- Argument 1: `(string)` The property to set
- Argument 2: `(mixed)` The value to set
- Return: `(self)` An instance of the current model

This method is run any time you attempt to set a property on the model: `$model->myProperty = 'myValue'`.

### `$model->setProperties((array) $properties)`

- Argument 1: `(array)` `$key => $value`s of properties to set
- Return: `(self)` An instance of the current model

### `$model->getProperty((string) $name)`

- Argument 1: `(string)` Name of the property to get
- Return: `(mixed)` The value of the property

### `$model->getDirtyValue((string) $name)`

- Argument 1: `(string)` Name of the property to get
- Return: `(mixed)` The unmodified value that was set on the property
    - This means the value has not been run through any data handlers or setters/getters

### `$model->asArray()`

- Return: (array) An array of the model values of the currently defined attributes
    - The keys in the returned array will be the property names
    - The values are the values after running through data handlers, getters, and setters
    
### `$model->validate()`

- Return: `(bool)` Returns `true` if all data on the model is valid. Returns `false` if any data on the model did not validate
- Running this method will also populate `$model->hasErrors`, and `$model->errors`
