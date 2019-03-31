<?php

declare(strict_types=1);

namespace buzzingpixel\executive\twigextensions;

use buzzingpixel\executive\services\EETemplateService;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;
use function json_decode;

class EETemplateTwigExtension extends AbstractExtension
{
    /** @var EETemplateService $eeTemplateService */
    private $eeTemplateService;

    public function __construct(EETemplateService $eeTemplateService)
    {
        $this->eeTemplateService = $eeTemplateService;
    }

    public function getFunctions() : array
    {
        return [
            new TwigFunction('renderEETemplate', [$this, 'renderTemplate']),
            new TwigFunction('renderEETemplateAsJson', [$this, 'renderTemplateAsJson']),
            new TwigFunction('renderEETemplatePath', [$this, 'renderPath']),
            new TwigFunction('renderEETemplatePathAsJson', [$this, 'renderPathAsJson']),
            new TwigFunction('renderEETemplateString', [$this, 'renderString']),
            new TwigFunction('renderEETemplateStringAsJson', [$this, 'renderStringAsJson']),
        ];
    }

    public function renderTemplate(
        string $group,
        string $template,
        array $variables = []
    ) : Markup {
        return new Markup(
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

    public function renderPath(string $path, array $variables = []) : Markup
    {
        return new Markup(
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

    public function renderString(string $str, array $variables = []) : Markup
    {
        return new Markup(
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
