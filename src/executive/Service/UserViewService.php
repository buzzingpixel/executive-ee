<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\Service;

use EllisLab\ExpressionEngine\Service\View\View;

/**
 * Class ViewService
 */
class UserViewService extends View
{
    /**
     * Get the full server path to the view file
     * @return string
     * @throws \Exception
     */
    protected function getPath()
    {
        $sysPath = rtrim(SYSPATH, '/');
        $filePath = "{$sysPath}/user/View/{$this->path}.php";

        if (! file_exists($filePath)) {
            throw new \Exception('View file not found: '.htmlentities($filePath));
        }

        return $filePath;
    }
}
