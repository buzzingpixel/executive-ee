<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services;

use function explode;
use function lcfirst;
use function preg_split;
use function ucfirst;

class CaseConversionService
{
    /**
     * Converts a string to PascaleCase
     */
    public function convertStringToPascale(string $str) : string
    {
        return $this->spaceConvert($this->underscoreConvert($str));
    }

    /**
     * Converts a string to camelCase
     */
    public function convertStringToCamel(string $str) : string
    {
        return lcfirst($this->convertStringToPascale($str));
    }

    /**
     * Converts underscores to PascaleCase
     */
    private function underscoreConvert(string $str) : string
    {
        $finalStr = '';

        foreach (explode('_', $str) as $item) {
            $finalStr .= ucfirst($item);
        }

        return $finalStr;
    }

    /**
     * Converts spaces to PascaleCase
     */
    private function spaceConvert(string $str) : string
    {
        $finalStr = '';

        foreach (preg_split('/\s+/', $str) as $item) {
            $finalStr .= ucfirst($item);
        }

        return $finalStr;
    }
}
