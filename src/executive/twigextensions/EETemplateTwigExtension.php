<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\twigextensions;

use Twig_Markup;
use Twig_Function;
use Twig_Extension;
use buzzingpixel\executive\services\EETemplateService;

class EETemplateTwigExtension extends Twig_Extension
{
    private $eeTemplateService;

    public function __construct(EETemplateService $eeTemplateService)
    {
        $this->eeTemplateService = $eeTemplateService;
    }

    public function getFunctions(): array
    {
        return [
            new Twig_Function('renderEETemplate', [$this, 'renderTemplate']),
            new Twig_Function('renderEETemplateAsJson', [$this, 'renderTemplateAsJson']),
            new Twig_Function('renderEETemplatePath', [$this, 'renderPath']),
            new Twig_Function('renderEETemplatePathAsJson', [$this, 'renderPathAsJson']),
            new Twig_Function('renderEETemplateString', [$this, 'renderString']),
            new Twig_Function('renderEETemplateStringAsJson', [$this, 'renderStringAsJson']),
        ];
    }

    public function renderTemplate(
        string $group,
        string $template,
        array $variables = []
    ): Twig_Markup {
        return new Twig_Markup(
            $this->eeTemplateService->renderTemplate(
                $group,
                $template,
                $variables
            ),
            'UTF-8'
        );
    }

    public function renderTemplateAsJson(
        string $group,
        string $template,
        array $variables = []
    ) {
        return json_decode(
            $this->eeTemplateService->renderTemplate(
                $group,
                $template,
                $variables
            ),
            true
        );
    }

    public function renderPath(string $path, array $variables = []): Twig_Markup
    {
        return new Twig_Markup(
            $this->eeTemplateService->renderPath($path, $variables),
            'UTF-8'
        );
    }

    public function renderPathAsJson(string $path, array $variables = [])
    {
        return json_decode(
            $this->eeTemplateService->renderPath($path, $variables),
            true
        );
    }

    public function renderString(string $str, array $variables = []): Twig_Markup
    {
        return new Twig_Markup(
            $this->eeTemplateService->renderString($str, $variables),
            'UTF-8'
        );
    }

    public function renderStringAsJson(string $str, array $variables = [])
    {
        return json_decode(
            $this->eeTemplateService->renderString($str, $variables),
            true
        );
    }
}
