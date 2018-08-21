<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\factories;

use Symfony\Component\Finder\SplFileInfo;

/**
 * Class SplFileInfoFactory
 */
class SplFileInfoFactory
{
    /**
     * Makes an instance of SplFileInfo
     * @param string $file,
     * @param string $relativePath
     * @param string $relativePathname
     * @return SplFileInfo
     */
    public function make(
        string $file,
        string $relativePath = '',
        string $relativePathname = ''
    ): SplFileInfo {
        return new SplFileInfo($file, $relativePath, $relativePathname);
    }
}
