<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services;

/**
 * Class CaseConversionService
 */
class CaseConversionService
{
    /**
     * Converts a string to PascaleCase
     * @param string $str
     * @return string
     */
    public function convertStringToPascale(string $str): string
    {
        return $this->spaceConvert($this->underscoreConvert($str));
    }

    /**
     * Converts a string to camelCase
     * @param string $str
     * @return string
     */
    public function convertStringToCamel(string $str): string
    {
        return lcfirst($this->convertStringToPascale($str));
    }

    /**
     * Converts underscores to PascaleCase
     * @param string $str
     * @return string
     */
    private function underscoreConvert(string $str): string
    {
        $finalStr = '';

        foreach (explode('_', $str) as $item) {
            $finalStr .= ucfirst($item);
        }

        return $finalStr;
    }

    /**
     * Converts spaces to PascaleCase
     * @param string $str
     * @return string
     */
    private function spaceConvert(string $str): string
    {
        $finalStr = '';

        foreach (preg_split('/\s+/', $str) as $item) {
            $finalStr .= ucfirst($item);
        }

        return $finalStr;
    }
}
