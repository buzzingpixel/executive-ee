# User Control Panel Views

Executive provides the ability for site developers to have custom control panel views for your site or app. Add them to the EE config like this:

```php
$config['cpSections'] = array(
    'myCpSection' => array(
        'index' => array(
            'title' => 'My CP Section',
            'class' => \myapp\controllers\mycpsection\IndexController::class,
            'method' => 'display'
        ),
        'anotherCpPage' => array(
            'title' => 'Another CP Section',
            'class' => \myapp\controllers\mycpsection\AnotherCpPageController::class,
            'method' => 'anotherMethod'
        ),
    ),
);
```

When preparing the class, Executive will first try to get the class from the [Dependency Injector](dependency-injection.md). This way, you can have fully dependency injected and unit tested code. If for some reason the class can't be retrieved from the DI, Executive will fall back to trying to new up the class.

Note that each section should have an `index` key. A section's index class/method is invoked by the following URL format:

`admin.php?/cp/addons/settings/executive&section=myCpSection`

To call a particular page within a section, use the following format:

`admin.php?/cp/addons/settings/executive&section=myCpSection&page=anotherCpPage`

Visiting Executive's Control Panel index page will list the available CP sections with a link to the index.

What your individual pages look like, including sidebar links, subsections, whatever, is entirely up to you. Your defined class methods are being called right from Executive's MCP class and the return of your class method is what is returned in Executive's MCP method.

## View files

To use EE's view service with your view files, you will need to tell Executive where your view files are at with the config file setting:

```php
$config['cpViewsBasePath'] = APP_DIR . '/src/views';
```

Then get Executive's slightly modified version of EE's view service:

```php
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\services\ViewService;

$viewService = ExecutiveDi::get(ViewService::class);
```

You should inject the view service into your class with the [dependency injector](dependency-injection.md). But however you do it, Executive's extension of the EE view service mostly works like EE's but observer the following: Before calling the `render()` method, make sure you set the view you want to use with the `->setView('my/view/file')` method.


```php
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\services\ViewService;

$viewService = ExecutiveDi::get(ViewService::class);

$renderedTemplate = $viewService->setView('CP/Index')->render([
    'myVar' => 'myVal',
])
```
