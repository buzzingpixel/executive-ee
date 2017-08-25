# DataModel Setting Data

You can set data on your model either by using the familiar `$model->myProperty = 'myValue'` or using the method `setProperty($name, $val)`. Both do exactly the same thing. The `setProperty` method returns an instance of the model for chaining like this:

```php
$modelArray = $model->setProperty('myProperty', 'myValue')
    ->setProperty('myOtherProp', 'someVal')
    ->asArray();
```

## Setting defined attributes

If the property is defined as a model attribute, then the appropriate methods are run for the specified data type when using the standard setting techniques referenced above.

## Custom Setters

Custom setter methods for your properties are defined by starting the method name with `set` and capitalizing the first letter of your property. So for instance, a custom setter method for the defined attribute of `myData` would be `setMyData`.

### Custom setters for non-existent attributes

You can use setters on non-existent properties by simply defining the method `setMyNonExistentProp`. In the case of a non-existent property the method received no arguments when run and return values do nothing. It is simply a method that is run when the non-existent property set is attempted.

### Custom setters for existing attributes

Setters for existing attributes receive two arguments:

1. The existing value of the property
    - The value is run through any data type handlers and setters first
2. The attribute definition

And the setter should return the value to be set for the property.

```php
protected function setMyProperty($existingVal, $def)
{
    if (isset($def['min']) && $existingVal < $def['min']) {
        return null;
    }
    
    return $existingVal;
}
```

### Custom definitions

You don't have to use any of the built in data types. You can set your own arbitrary attribute definitions and use a custom setter or no setter at all.

## Setting multiple properties at once

You can set multiple properties at once by using the `setProperties` method which received an array of `$key => $values` to set on the model. If you send a property that is not defined or has no setter, it will simply not be set.
