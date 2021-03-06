# User Template Tags

Executive provides the ability to register custom template tags in the EE config file.

```php
$config['tags'] = [
    'sample_tag' => [
        'class' =>  myapp\commands\SampleTag::class,
        'method' => 'myTag',
    ],
];
```

## Using your template tags

To use the template tag, use the following template syntax:

```
{!-- Single Tag --}
{exp:executive:user:sample_tag param="val"}

{!-- Tag Pair --}
{exp:executive:user:another_tag param="val"}
    {my_var}
{/exp:executive:user:another_tag}
```

When preparing the class, Executive will first try to get the class from the [Dependency Injector](dependency-injection.md). This way, you can have fully dependency injected and unit tested code. If for some reason the class can't be retrieved from the DI, Executive will fall back to trying to new up the class.
