# EE Template Service

The EE Template service provides a means of rendering an EE template, or a file anywhere in your file system, or just passing it a string to render, and getting back a string of the rendered template. It's pretty simple to use. Here are examples of using all the methods.

```php
<?php
declare(strict_types=1);

use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\services\EETemplateService;

class MyController
{
    /** @var EETemplateService $templateService */
    private $templateService;
    
    public function __construct()
    {
        $this->templateService = ExecutiveDi::diContainer()->get(EETemplateService::class);
    }

    public function renderEETemplate()
    {
        $renderedTemplateString = $this->templateService->renderTemplate(
            'template_group',
            'template',
            [
                'optional1' => 'An optional array of key => value variables',
                'optional2' => 'To make available to the template',
            ]
        );
    }

    public function renderTemplateFromPath()
    {
        $renderedTemplateString = $this->templateService->renderPath(
            // Provide a path relative to APP_DIR constant or an absolute path
            'relative/path/to/template.html',
            [
                'optional1' => 'An optional array of key => value variables',
                'optional2' => 'To make available to the template',
            ]
        );
    }

    public function renderTemplateFromString()
    {
        $renderedTemplateString = $this->templateService->renderString(
            'string template',
            [
                'optional1' => 'An optional array of key => value variables',
                'optional2' => 'To make available to the template',
            ]
        );
    }
}
```
