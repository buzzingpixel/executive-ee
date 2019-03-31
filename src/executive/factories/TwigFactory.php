<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\twigextensions\EETemplateTwigExtension;
use EE_Config;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use const DIRECTORY_SEPARATOR;
use function is_array;
use function is_dir;
use function mkdir;

class TwigFactory
{
    public function get()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $di = ExecutiveDi::diContainer();

        /** @var EE_Config $config */
        $config = ee()->config;
        $debug  = $config->item('debug', 'twig');
        $debug  = $debug === 'true' || $debug === true ||
            $debug === 1 || $debug === '1' ||
            $debug === 'y' || $debug === 'yes';

        $templatesPath = $config->item('templatesPath', 'twig');

        $loader = new FilesystemLoader($templatesPath);

        $sep = DIRECTORY_SEPARATOR;

        $twigCacheDir = SYSPATH . 'user' . $sep . 'cache' . $sep . 'twig';

        if (! is_dir($twigCacheDir)) {
            mkdir($twigCacheDir, 0777, true);
        }

        $twig = new Environment($loader, [
            'debug' => $debug,
            'cache' => $twigCacheDir,
            'strict_variables' => $debug,
        ]);

        $globals = $config->item('globals', 'twig');
        $globals = is_array($globals) ? $globals : [];

        foreach ($globals as $key => $val) {
            $twig->addGlobal($key, $val);
        }

        $extensions = $config->item('extensions', 'twig');
        $extensions = is_array($extensions) ? $extensions : [];

        foreach ($extensions as $extension) {
            $instantiatedClass = null;

            if ($di->has($extension)) {
                $instantiatedClass = $di->get($extension);
            }

            if (! $instantiatedClass) {
                $instantiatedClass = new $extension();
            }

            $twig->addExtension($instantiatedClass);
        }

        $twig->addExtension($di->get(EETemplateTwigExtension::class));

        if ($debug) {
            $twig->addExtension(new DebugExtension());
        }

        return $twig;
    }
}
