# User Template Tags

Executive provides the ability to register your own custom template tags per-site/project.

## Generating a template tag skeleton class

Use the following command

```bash
php ee executive makeTag --description=MyTag
```

## Registering your template tag

In the EE config file, use the following format to register template tags:

```php
$config['tags'] = array(
    'my_tag' => array(
        'class' => '\User\Tag\MyTag',
        'method' => 'myTag',
    ),
    'another_tag' => array(
        'class' => '\User\Tag\AnotherTag',
        'method' => 'myTag',
    ),
);
```

## Using your template tag

To use the template tag, use the following template syntax:

```
{!-- Single Tag --}
{exp:executive:user:my_tag param="val"}

{!-- Tag Pair --}
{exp:executive:user:another_tag param="val"}
    {my_var}
{/exp:executive:user:another_tag}
```
