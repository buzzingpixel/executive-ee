# DataModel Getting Data

You can get data from your model either by using the familiar `$model->myProperty` or using the method `getProperty('name')`. Both do exactly the same thing.

```php
$myPropertyValue = $model->myProperty;
$myOtherPropertyValue = $model->getProperty('myOtherProperty');
```

## Custom getters

Custom getter methods for your properties are defined by starting the method with `get` and capitalizing the first letter of your property. So for instance, a custom getter method for the defined attribute of `myData` would be `getMyData`.

### Custom getters for non-existent attributes

You can use getters on non-existent properties by simply defining the method `getMyNonExistentProp`. Whatever you return from that method will be accessible at `$model->myNonExistentProp`.

### Custom getters for existing attributes

Getters for existing attributes receive two arguments:

1. The existing value of the property
    - The value is run through any data type handlers and getters first
2. The attribute definition

And the getter should return the value you want to be returned for that property

```php
protected function getMyProperty($existingVal, $def)
{
    if (isset($def['min']) && $existingVal < $def['min']) {
        return null;
    }
    
    return $existingVal;
}
```
