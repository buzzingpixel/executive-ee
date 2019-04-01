<?php

declare(strict_types=1);

namespace BuzzingPixel\Executive\services;

use buzzingpixel\minify\interfaces\MinifyApiInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

class TwigEnvironment extends Environment
{
    /** @var MinifyApiInterface $minifyApi */
    private $minifyApi;

    public function __construct(
        LoaderInterface $loader,
        array $options,
        MinifyApiInterface $minifyApi
    ) {
        parent::__construct($loader, $options);

        $this->minifyApi = $minifyApi;
    }

    public function getLoader() : FilesystemLoader
    {
        /** @var FilesystemLoader $fileSystemLoader */
        $fileSystemLoader = parent::getLoader();

        return $fileSystemLoader;
    }

    public function renderAndMinify(
        string $template,
        array $context = [],
        array $minifyOptions = []
    ) : string {
        return $this->minifyApi->minifyHtml(
            $this->render($template, $context),
            $minifyOptions
        );
    }
}
