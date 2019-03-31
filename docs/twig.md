# Twig support in Executive

Executive 3 introduces the ability to render Twig templates. Of course with the routes you could instantiate your own Twig instance, render the Twig template, and write the string to a ResponseInterface Body, but Executive includes Twig and you can just call it up from the dependency injector and render your Twig templates to write to the ResponseInterface Body.

```php
<?php
declare(strict_types=1);

use Zend\Diactoros\Response;
use buzzingpixel\executive\ExecutiveDi;
use Twig\Environment as TwigEnvironment;
use buzzingpixel\executive\models\RouteModel;

class SomeController
{
    public function route(RouteModel $router)
    {
        $response = new Response();

        $response = $response->withStatus(200)
            ->withHeader('Content-Type', 'text/html');

        $response->getBody()->write(
            ExecutiveDi::diContainer()->get(TwigEnvironment::class)->render(
                'TestTwigTemplate.twig',
                [
                    'testVar' => 'thingy',
                ]
            )
        );

        return $response;
    }
}
```

## Config

```php
$config['twig'] = [
    'debug' => true,
    'templatesPath' => APP_DIR . '/src/views',
    'extensions' => [
        SomeTwigExtensionClass::class
    ],
];
```

### `$config['twig']['debug']`

Accepts boolean of `true` or `false`. This should be `false` in production environments and `true` in development environments. When set to `true` as well as enabling Twig's native debug mode, strict variables will be enabled, and the Twig debug extension will be attached to the Twig instance, which, among other things, allows the use of the `{{ dump(myVar) }}` function.

### `$config['twig']['templatesPath']`

Twig will need to know where to point the template loader to look for your Twig templates, so you'll need to add this config item telling Twig the absolute path to where your templates directory is.

### `$config['twig']['extensions']`

You can attach your own extension classes to the Twig instance with this array. Executive will attempt to get them from the dependency injector and falls back to newing up the class. If the class doesn't exist, an exception will be thrown.

## EE Template Functions in Twig

Because some modules and other things in EE expect to be in, and may only work in an EE template context, Executive provides several functions to Twig to help you you.

### `{{ renderEETemplate('template_group', 'template', {optional_var: 'val'}) }}`

The `renderEETemplate` function renders the specified template and returns the string. The third parameter is an option set of key value variables to send to the template on render. If you need to use loops and tag pairs in the specified template, use the [RoutingModel](custom-routing.md#routing-model) (this applies to all EE Template rendering methods).

### `{{ renderEETemplateAsJson('template_group', 'template', ['optional_var' => 'val']) }}`

The `renderEETemplateAsJson` function renders an EE template as above, but `json_decodes` the rendered string for you so that you can return json from EE templates and then use the resulting array in Twig.

### `{{ renderEETemplatePath('path/to/template', {optional_var: 'val'}) }}`

The `renderEETemplatePath` function renders a template file from the specified path and returns the string. The third parameter is an option set of key value variables to send to the template on render.

### `{{ renderEETemplatePathAsJson('path/to/template', {optional_var: 'val'}) }}`

The `renderEETemplatePathAsJson` function renders an EE template from a file as above, but `json_decodes` the rendered string for you so that you can return json from EE templates and then use the resulting array in Twig.

### `{{ renderEETemplateString('string to render', {optional_var: 'val'}) }}`

The `renderEETemplateString` function renders an EE template from the input string and returns the rendered string. The third parameter is an option set of key value variables to send to the template on render. This is great if you want to do some quick EE template code right in line in your Twig template and get back the result.

```twig
{% set eeTemplateString %}
    {optional_var}<br>
    {another_var}<br>
    {exp:channel:entries}
        {title}<br>
    {/exp:channel:entries}
    <br><br>
{% endset %}
{{ renderEETemplateString(eeTemplateString, {
    optional_var: 'val',
    another_var: 'asdf'
}) }}
```

### `{{ renderEETemplateStringAsJson('path/to/template', {optional_var: 'val'}) }}`

The `renderEETemplateStringAsJson` function renders an EE template from the input string as above, but `json_decodes` the rendered string for you so that you can return json from EE templates and then use the resulting array in Twig.

```twig
{% set eeTemplateString %}
    {
        "test": {optional_var:json},
        "test2": {another_var:json},
        "entries": [
        {exp:channel:entries dynamic="no" limit="{limit}" channel="{channel}"}
            {
                "title": {title:json},
                "url_title": {url_title:json}
            }{if count != total_results},{/if}
        {/exp:channel:entries}
        ]
    }
{% endset %}
{% set results = renderEETemplateStringAsJson(eeTemplateString, {
    optional_var: 'val',
    another_var: 'asdf',
    limit: 10,
    channel: 'test_channel',
}) %}

{{ results.test }}<br>
{{ results.test2 }}<br>
<br>
{% for entry in results.entries %}
    {{ entry.title }} - {{ entry.url_title }}<br>
{% endfor %}
```

## Rendering Twig Templates in EE Templates

Yes, the reverse has also been made possible by Executive. Executive makes no assumptions about how you want to use it's components. As such, Executive provides a way to render a Twig template from in an EE template. Here's an example of how it's done:

Provide variables via JSON in a tag pair:

```ee
{exp:executive:render_twig_template template="Path/To/TwigTemplate.twig"}
    {
        "testVar": "stuff"
    }
{/exp:executive:render_twig_template}
```

Or provide variables via JSON to the template in a parameter.

```ee
{exp:executive:render_twig_template template='Path/To/TwigTemplate.twig' vars='{
    "testVar": "asdf"
}'}
```
