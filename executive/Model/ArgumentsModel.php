<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

namespace BuzzingPixel\Executive\Model;

use BuzzingPixel\DataModel\Model as DataModel;
use BuzzingPixel\DataModel\DataType;

/**
 * Class ArgumentsModel
 * @property array $rawArgs
 * @property-read $parsedArgs
 */
class ArgumentsModel extends DataModel
{
    /** @var array $parsedArgs */
    private $parsedArgsStorage;

    /**
     * Define attributes
     * @return array
     */
    public function defineAttributes()
    {
        return array(
            'rawArgs' => DataType::ARR,
        );
    }

    /**
     * Parse raw args
     */
    private function parseArgs()
    {
        $parsedArgs = array();

        foreach ($this->rawArgs as $key => $rawArg) {
            if ($key === 0) {
                $parsedArgs['group'] = $rawArg;
                continue;
            } elseif ($key === 1) {
                $parsedArgs['command'] = $rawArg;
                continue;
            }

            if (strpos($rawArg, '--') !== 0) {
                continue;
            }

            $rawArg = explode('--', $rawArg);
            unset($rawArg[0]);
            $rawArg = $rawArg[1];

            $rawArg = explode('=', $rawArg);

            $parsedArgs[$rawArg[0]] = isset($rawArg[1]) ? $rawArg[1] : null;
        }

        $this->parsedArgsStorage = $parsedArgs;
    }

    /**
     * Get parsed args
     */
    public function getParsedArgs()
    {
        if ($this->parsedArgsStorage === null) {
            $this->parseArgs();
        }

        return $this->parsedArgsStorage;
    }

    /**
     * Get argument
     * @param string $key
     * @return string|null
     */
    public function getArgument($key)
    {
        if (! isset($this->parsedArgs[$key])) {
            return null;
        }

        return $this->parsedArgs[$key];
    }
}
