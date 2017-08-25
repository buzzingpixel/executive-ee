# DataModel Default Properties

In addition to whatever [custom properties](custom-properties.md) you set in each of your models with the `defineAttributes` method, all models also have 3 default properties.

## `uuid`

Also available from the method `getUuid`.

On construction of a model, each model gets a unique ID.

## `hasErrors`

Also available from the method `getHasErrors`.

After the model's `validate` method has been called, if there are any errors on the model, this will be `(bool) true`. If there are no errors, or the `validate` method has not been called on the model, this will be `(bool) false`.

## `errors`

Also available from the method `getErrors`.

After the model's `validate` method has been called, this will contain an array of the errors. The keys in this array will be the property name and the value will be an array listing the errors on that property.
