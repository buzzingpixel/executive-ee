# DataModel Custom Properties

In addition to the 3 [default properties](default-properties.md) every model has, models will have the properties you define. The primary means by which you define properties is with the `defineAttributes` method.

You can also have custom properties on a model by defining a method prefixed by `get`. So for instance if you wanted a model to have the property `location` that always return a fixed string, you could so this:

```php
/**
 * Class MyModel
 * 
 * @property string $location
 */
class MyModel extends Model
{
    /**
     * Location getter
     */
    public function getLocation()
    {
        return 'The United States';
    }
}
```

This works to provide the model class property `location` without having to define an attribute. But `getMyProperty` will also work as a custom getter for defined attributes as well.
