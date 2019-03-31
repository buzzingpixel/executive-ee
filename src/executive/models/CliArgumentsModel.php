<?php

declare(strict_types=1);

namespace buzzingpixel\executive\models;

use function array_values;
use function explode;
use function mb_strpos;

class CliArgumentsModel
{
    /** @var array $rawArguments */
    private $rawArguments = [];
    /** @var array $parsedArguments */
    private $parsedArguments = [];

    /**
     * CliArgumentsModel constructor
     */
    public function __construct(
        array $rawArguments = []
    ) {
        $this->setRawArguments($rawArguments);
    }

    /**
     * Sets raw arguments array
     *
     * @param bool $omitFirst Defaults to true
     *
     * @return CliArgumentsModel
     */
    public function setRawArguments(
        array $rawArguments,
        bool $omitFirst = true
    ) : self {
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

            if (mb_strpos($rawArgument, '--') !== 0) {
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
     */
    public function getRawArguments() : array
    {
        return $this->rawArguments;
    }

    /**
     * Gets parsed arguments
     */
    public function getParsedArguments() : array
    {
        return $this->parsedArguments;
    }

    /**
     * Gets a specific argument
     */
    public function getArgument(string $key) : ?string
    {
        return $this->parsedArguments[$key] ?? null;
    }
}
