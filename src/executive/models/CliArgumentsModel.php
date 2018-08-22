<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\models;

/**
 * Class CliArgumentsModel
 */
class CliArgumentsModel
{
    /** @var array $rawArguments */
    private $rawArguments = [];

    /** @var array $parsedArguments */
    private $parsedArguments = [];

    /**
     * CliArgumentsModel constructor
     * @param array $rawArguments
     */
    public function __construct(
        array $rawArguments = []
    ) {
        $this->setRawArguments($rawArguments);
    }

    /**
     * Sets raw arguments array
     *
     * @param array $rawArguments
     * @param bool $omitFirst Defaults to true
     * @return CliArgumentsModel
     */
    public function setRawArguments(
        array $rawArguments,
        bool $omitFirst = true
    ): self {
        $rawArguments = array_values($rawArguments);

        if ($omitFirst) {
            unset($rawArguments[0]);
        }

        $rawArguments = array_values($rawArguments);

        $this->rawArguments = $rawArguments;

        $parsedArguments = [];

        foreach ($rawArguments as $key => $rawArgument) {
            if ($key === 0) {
                $parsedArguments['group'] = $rawArgument;
                continue;
            }

            if ($key === 1) {
                $parsedArguments['command'] = $rawArgument;
                continue;
            }

            if (strpos($rawArgument, '--') !== 0) {
                continue;
            }

            $rawArgument = explode('--', $rawArgument);
            unset($rawArgument[0]);
            $rawArgument = $rawArgument[1];

            $rawArgument = explode('=', $rawArgument);

            $parsedArguments[$rawArgument[0]] = $rawArgument[1] ?? null;
        }

        $this->parsedArguments = $parsedArguments;

        return $this;
    }

    /**
     * Gets raw arguments
     * @return array
     */
    public function getRawArguments(): array
    {
        return $this->rawArguments;
    }

    /**
     * Gets parsed arguments
     * @return array
     */
    public function getParsedArguments(): array
    {
        return $this->parsedArguments;
    }

    /**
     * Gets a specific argument
     * @param string $key
     * @return string|null
     */
    public function getArgument(string $key): ?string
    {
        return $this->parsedArguments[$key] ?? null;
    }
}
