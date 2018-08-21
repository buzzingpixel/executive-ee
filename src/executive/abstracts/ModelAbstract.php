<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 */

namespace buzzingpixel\executive\abstracts;

/**
 * Abstract Class ModelAbstract
 */
abstract class ModelAbstract
{
    /**
     * FieldSettingsModel constructor
     * @param array $properties Properties to instantiate the model with
     */
    public function __construct(array $properties = [])
    {
        $this->setPropertiesFromArray($properties);
    }

    /**
     * Convert underscore_type string to PascalCase type string
     * @param string $str
     * @return string
     */
    private function convertStringToPascalCase(string $str): string
    {
        $finalStr = '';

        foreach (explode('_', $str) as $item) {
            $finalStr .= ucfirst($item);
        }

        return $finalStr;
    }

    /**
     * Sets properties from array
     * @param array $properties
     */
    public function setPropertiesFromArray(array $properties): void
    {
        foreach ($properties as $prop => $val) {
            $setMethod = "set{$this->convertStringToPascalCase($prop)}";

            if (! method_exists($this, $setMethod)) {
                continue;
            }

            $this->{$setMethod}($val);
        }
    }
}
