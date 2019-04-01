<?php

declare(strict_types=1);

use BuzzingPixel\Executive\services\TwigEnvironment;
use buzzingpixel\executive\twigextensions\EETemplateTwigExtension;
use buzzingpixel\minify\interfaces\MinifyApiInterface;
use corbomite\configcollector\Factory as CollectorFactory;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

return [
    'ExecutiveTwig.TemplateDirectories' => static function () {
        $dirs = [];

        $collector = CollectorFactory::collector();

        $appBasePath = APP_DIR;

        $item = $collector->getExtraKeyFromPath($appBasePath, 'twigTemplatesDirectories');
        $item = is_array($item) ? $item : [];

        foreach ($item as $k => $v) {
            $dirs[$k] = $appBasePath . DIRECTORY_SEPARATOR . $v;
        }

        $vendorIterator = CollectorFactory::directoryIterator(
            $appBasePath . DIRECTORY_SEPARATOR . 'vendor'
        );

        foreach ($vendorIterator as $fileInfo) {
            if ($fileInfo->isDot() || ! $fileInfo->isDir()) {
                continue;
            }

            $providerIterator = CollectorFactory::directoryIterator(
                $fileInfo->getPathname()
            );

            foreach ($providerIterator as $providerFileInfo) {
                if ($providerFileInfo->isDot() ||
                    ! $providerFileInfo->isDir()
                ) {
                    continue;
                }

                $item = $collector->getExtraKeyFromPath(
                    $providerFileInfo->getPathname(),
                    'twigTemplatesDirectories'
                );
                $item = is_array($item) ? $item : [];

                foreach ($item as $k => $v) {
                    $dirs[$k] = $providerFileInfo->getPathname() . DIRECTORY_SEPARATOR . $v;
                }
            }
        }

        return $dirs;
    },
    TwigEnvironment::class => static function (ContainerInterface $di) {
        $sep = DIRECTORY_SEPARATOR;

        $twigCacheDir = SYSPATH . 'user' . $sep . 'cache' . $sep . 'twig';

        if (! is_dir($twigCacheDir)) {
            mkdir($twigCacheDir, 0777, true);
        }

        $config = $di->get(EE_Config::class);

        $debug = $config->config['twig']['debug'] ?? getenv('DEV_MODE') === 'true';

        $debug = $debug === 'true' || $debug === true ||
            $debug === 1 || $debug === '1' ||
            $debug === 'y' || $debug === 'yes';

        $minifyApi = $di->get(MinifyApiInterface::class);

        $twig = new TwigEnvironment(
            new FilesystemLoader(),
            [
                'debug' => $debug,
                'cache' => $twigCacheDir,
                'strict_variables' => $debug,
            ],
            $minifyApi
        );

        $collector = CollectorFactory::collector();

        foreach ($collector->collect('twigGlobalsFilePath') as $key => $val) {
            $twig->addGlobal($key, $val);
        }

        $globals = $config->item('globals', 'twig');
        $globals = is_array($globals) ? $globals : [];

        foreach ($globals as $key => $val) {
            $twig->addGlobal($key, $val);
        }

        foreach ($collector->getExtraKeyAsArray('twigExtensions') as $twigExtension) {
            $class = null;

            if ($di->has($twigExtension)) {
                $class = $di->get($twigExtension);
            }

            if (! $class) {
                $class = new $twigExtension();
            }

            $twig->addExtension($class);
        }

        $extensions = $config->item('extensions', 'twig');
        $extensions = is_array($extensions) ? $extensions : [];

        foreach ($extensions as $extension) {
            $class = null;

            if ($di->has($extension)) {
                $class = $di->get($extension);
            }

            if (! $class) {
                $class = new $extension();
            }

            $twig->addExtension($class);
        }

        $twig->addExtension($di->get(EETemplateTwigExtension::class));

        foreach ($di->get('ExecutiveTwig.TemplateDirectories') as $n => $p) {
            $loader = $twig->getLoader();

            $namespace = $n ?: $loader::MAIN_NAMESPACE;

            /** @noinspection PhpUnhandledExceptionInspection */
            $loader->addPath($p, $namespace);
        }

        $templatesPath = $config->item('templatesPath', 'twig');

        if ($templatesPath) {
            $loader = $twig->getLoader();

            $loader->addPath($loader::MAIN_NAMESPACE, $templatesPath);
        }

        if ($debug) {
            $twig->addExtension(new DebugExtension());
        }

        return $twig;
    },
    Environment::class => static function (ContainerInterface $di) {
        return $di->get(TwigEnvironment::class);
    },
];
