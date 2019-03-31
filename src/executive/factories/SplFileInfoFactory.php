<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use Symfony\Component\Finder\SplFileInfo;

class SplFileInfoFactory
{
    /**
     * Makes an instance of SplFileInfo
     *
     * @param string $file,
     */
    public function make(
        string $file,
        string $relativePath = '',
        string $relativePathname = ''
    ) : SplFileInfo {
        return new SplFileInfo($file, $relativePath, $relativePathname);
    }
}
