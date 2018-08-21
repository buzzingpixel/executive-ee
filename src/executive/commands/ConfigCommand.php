<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\commands;

use EE_Lang;
use EE_Config;
use buzzingpixel\executive\services\CliQuestionService;

/**
 * Class ConfigCommand
 */
class ConfigCommand
{
    /** @var EE_Config $config */
    private $config;

    /** @var EE_Lang $lang */
    private $lang;

    /** @var CliQuestionService $cliQuestionService */
    private $cliQuestionService;

    /**
     * ConfigCommand constructor
     * @param EE_Config $config
     * @param EE_Lang $lang
     * @param CliQuestionService $cliQuestionService
     */
    public function __construct(
        EE_Config $config,
        EE_Lang $lang,
        CliQuestionService $cliQuestionService
    ) {
        $this->config = $config;
        $this->lang = $lang;
        $this->cliQuestionService = $cliQuestionService;
    }

    /**
     * Get config item
     * @param string $key
     * @param string $index
     */
    public function get($key, $index): void
    {
        if ($key === null) {
            $key = $this->cliQuestionService->ask(
                '<fg=cyan>' .
                $this->lang->line('configKey') .
                ': </>'
            );
        }

        $val = $this->config->item($key, $index);

        print_r('(' . gettype($val) . ') ');
        print_r($val);
        echo PHP_EOL;
    }
}
