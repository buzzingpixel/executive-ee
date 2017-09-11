# User Control Panel Views

Executive provides the ability for site developers to have custom control panel views for your site or app. Add them to the EE config like this:

```php
$config['cpSections'] = array(
    'myCpSection' => array(
        'index' => array(
            'title' => 'My CP Section',
            'class' => '\User\CP\MyCpSection',
            'method' => 'myMethod'
        ),
        'anotherCpPage' => array(
            'title' => 'Another CP Section',
            'class' => '\User\CP\MyCpSection',
            'method' => 'anotherMethod'
        ),
    ),
);
```

Note that each section should have an `index` key. A section's index class/method is invoked by the following URL format:

`admin.php?/cp/addons/settings/executive&section=myCpSection`

To call a particular page within a section, use the following format:

`admin.php?/cp/addons/settings/executive&section=myCpSection&page=anotherCpPage`

Visiting Executive's Control Panel index page will list the available CP sections with a link to the index. What that page looks like, including sidebar links, subsections, whatever, is entirely up to you. It's just like any add-on view.

## View files

Observe the following exception to the above statement that it's "just like any add-on view". You should use Executive's `UserView`, instead of `ee('View')` so that ExpressionEngine will look in `system/user/View` for your view file. Call it like this:

```php
ee('executive:UserView', 'path/to/my/viewfile')->render(array(
    'myVar' => 'MyVal'
))
```
