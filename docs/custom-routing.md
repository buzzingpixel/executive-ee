# Custom Routing

Executive offers the ability to set routes that route to class methods. Here's an example of how it works:

```php
$config['customRoutes'] = [
    // :before is a special route that runs before any other routes
    ':before' => [
        'class' => \test\Test::class,
        'method' => 'before',
    ],
    
    // :home is a special route that matches for the home page
    ':home' => [
        'class' => \test\Test::class,
        'method' => 'home',
    ],
    
    // :after is a special route that is run after all other routes
    ':after' => [
        'class' => \test\Test::class,
        'method' => 'after'
    ],
    
    // :catch is a special route that is run if no other routes match
    ':catch' => [
        'class' => \test\Test::class,
        'method' => 'catch',
    ],
    
    // There are several wild-card's you can use in your routes which will be
    // documented below this code block
    '/stuff/thing/:any/' => [
        'class' => \test\Test::class,
        'method' => 'run',
    ],

    // And routes are just regex, so you can use regex, mix and match with
    // wild-cards and more
    'blog(/category/:any)?(/page/:num)?' => [
        'class' => \controllers\BlogController::class,
        'method' => 'list',
    ],
    'blog/:any' => [
        'class' => \controllers\BlogController::class,
        'method' => 'showEntry',
    ],
];
```

## Wild-cards

- `:any` Matches any character in a url segment (i.e. any character except `/`). The regular expression is `([^/]+)`
- `:num` Matches a numeric value. The regular expression is `(\d+)`
- `:year` Matches a 4 digit numeric value. The regular expression is `(\d{4})`
- `:month` Matches a 2 digit numeric value. The regular expression is `(\d{2})`
- `:day` Matches a 2 digit numeric value. The regular expression is `(\d{2})`
- `:pagination` Matches a P:num segment. The regular expression is `((?:/P\d+)?)`
- `:all` Matches all possible segments. The regular expression is `((?:/.*)?)`. This is an optional segment. If not present in the URI, the URI will still be considered a match.

Routes are checked for a match and run in the order they are specified in the array. All matched routes are run unless a route calls `setStop()` on the RouteModel it receives.

## Calling the custom method

When preparing the class to run, Executive will first try to get the class from the [Dependency Injector](dependency-injection.md). This way, you can have fully dependency injected and unit tested code. If you have not defined the class in the dependency injector config, Executive will fall back to trying to new up the class.

### Routing Model

The first argument the specified method will always receive is the RouteMode: `\buzzingpixel\executive\models\RouteModel`

#### `setTemplate('template_group/template_name')`

This is how you set a template for EE to render on the route

#### `getTemplate()`

Returns the currently set template in case you need to see what it is. It may have been set by you in the currently matched route, or in a previously matched route.

#### `hasTemplate()`

Returns `true` if a template is set.

#### `set404()`

If you want to send the 404 signal to EE, use this method. You can alternately pass an argument of `false` if you want to make sure EE doesn't 404.

If you want to 404 immediately and not go on with routing or the rest of your method, you can do also use the `setStop()` method and return out of your method.

#### `get404()`

Returns true if a 404 has been requested.

#### `setStop()`

This will stop further routes from being evaluated and run. Optionally pass an argument of false to make sure routing continues.

#### `getStop()`

Returns `true` if `setStop()` has been requested.

#### `setVariable('my_var', 'my_val')`

Sets a variable to be available in the rendered template.

#### `setVariables(['var_1' => 'val_1, 'var_2' => 'val_2'])`

Set multiple variables at once from an array to be available in the rendered template.

#### `getVariable('my_var')`

Gets the specified variable.

#### `getVariables()`

Gets the array of variables.

#### `setPair('pair_name', [])`

Set a variable pair to be available in the rendered template. Use `{exp:executive:route_pair}` to retrieve the pair:

```php
$routeModel->setPair('testing', [
    [
        'my_var' => 'my_value'
    ],
    [
        'my_var' => 'another_value',
    ]
]);
```

```ee
{exp:executive:route_pair name="my_pair"}
    {my_var}
{/exp:executive:route_pair}
```

#### `getPair('my_pair')`

Gets a pair that has been set.

#### `getPairs()`

Gets the array of variable pairs.

#### `getResponse()`

Gets the `\Psr\Http\Message\ResponseInterface` if it has been set (by returning an instance of `ResponseInterface` from another routed method), otherwise returns null. There will be more on this in a moment, but the ResponseInterface is an alternative to EE templating.

## Returning a custom response object

When using Executive Routing, you have the option of returning a `\Psr\Http\Message\ResponseInterface` from your route's called method. When you do this, that response will be emitted instead of continuing on to EE's templating engine and letting EE emit the response.

Executive includes Zend Diactoros as a dependency, so you can use the Diactoros response object which implements the `ResponseInterface` if you would like (or you can use any class that implements `ResponseInterface`). Here's an example:

```php
<?php
declare(strict_types=1);

namespace src;

use Zend\Diactoros\Response;
use buzzingpixel\executive\models\RouteModel;

class SomeController
{
    public function __invoke(RouteModel $router)
    {
        $response = new Response();

        $response = $response->withStatus(200)
            ->withHeader('Content-Type', 'text/html');

        $response->getBody()->write('Your HTML will go here');

        return $response;
    }
}
```
