<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\factories;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\twigextensions\EETemplateTwigExtension;

class TwigFactory
{
    public function get()
    {
        /** @var \EE_Config $config */
        $config = ee()->config;
        $debug = $config->item('debug', 'twig');
        $debug = $debug === 'true' || $debug === true ||
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
        $globals = \is_array($globals) ? $globals : [];

        foreach ($globals as $key => $val) {
            $twig->addGlobal($key, $val);
        }

        $extensions = $config->item('extensions', 'twig');
        $extensions = \is_array($extensions) ? $extensions : [];

        foreach ($extensions as $extension) {
            $instantiatedClass = null;

            if (ExecutiveDi::has($extension)) {
                $instantiatedClass = ExecutiveDi::get($extension);
            }

            if (! $instantiatedClass) {
                $instantiatedClass = new $extension();
            }

            $twig->addExtension($instantiatedClass);
        }

        $twig->addExtension(ExecutiveDi::get(EETemplateTwigExtension::class));

        if ($debug) {
            $twig->addExtension(new DebugExtension());
        }

        return $twig;
    }
}
