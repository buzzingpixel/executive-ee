<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services;

use buzzingpixel\executive\exceptions\InvalidViewConfigurationException;
use EllisLab\ExpressionEngine\Core\Provider;
use EllisLab\ExpressionEngine\Service\View\View;
use const DIRECTORY_SEPARATOR;
use function class_exists;
use function dirname;
use function explode;
use function file_exists;
use function mb_strpos;
use function rtrim;
use function str_replace;

/**
 * When running provisioning or installation from the command line
 * before EE is actually booted up, the View class will not be available.
 * But because we're calling ::class off this class for dependency injection,
 * we're gonna have a bad time
 */
if (! class_exists(View::class)) {
    require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'stubs' .
        DIRECTORY_SEPARATOR . 'ExpressionEngineViewStub.php';
}

class ViewService extends View
{
    public const INTERNAL_DI_NAME = 'ExecutiveViewService';

    /** @var string $viewsBasePath */
    private $viewsBasePath;

    /**
     * ViewService constructor
     */
    public function __construct(
        Provider $provider,
        string $viewsBasePath
    ) {
        parent::__construct('', $provider);

        $this->viewsBasePath = rtrim(
            rtrim($viewsBasePath, '/'),
            DIRECTORY_SEPARATOR
        );
    }

    /**
     * Sets the relative path to the view file
     */
    public function setView(string $view) : self
    {
        $this->path = $view;

        return $this;
    }

    /**
     * Get the full server path to the view file
     *
     * @throws InvalidViewConfigurationException
     */
    public function getPath() : string
    {
        $sep = DIRECTORY_SEPARATOR;

        $filePath = $this->viewsBasePath . $sep . $this->path . '.php';

        if (! file_exists($filePath)) {
            throw new InvalidViewConfigurationException(
                str_replace(
                    '{{filePath}}',
                    $filePath,
                    lang('viewFileNotFound')
                )
            );
        }

        return $filePath;
    }

    /**
     * Makes a new view
     *
     * @return ViewService
     */
    public function make(string $view) : View
    {
        /**
         * If the view has a colon, we should get EE's actual view service,
         * because we're trying to get another provider's view
         */
        if (mb_strpos($view, ':')) {
            $provider = $this->provider;

            [$prefix, $view] = explode(':', $view, 2);

            if ($provider->getPrefix() !== $prefix) {
                $provider = $provider->make('App')->get($prefix);
            }

            return new View($view, $provider);
        }

        $newView = new static($this->provider, $this->viewsBasePath);
        $newView->setView($view);

        return $newView;
    }

    /**
     * Renders the view
     *
     * @param array $vars
     *
     * @throws InvalidViewConfigurationException
     */
    public function render(array $vars = []) : string
    {
        if (! $this->viewsBasePath) {
            throw new InvalidViewConfigurationException(
                lang('pleaseSetCpViewsBasePath')
            );
        }

        if (! $this->path) {
            throw new InvalidViewConfigurationException(
                lang('pleaseSetView')
            );
        }

        return parent::render($vars);
    }
}
