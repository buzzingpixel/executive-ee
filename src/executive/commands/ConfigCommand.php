<?php

declare(strict_types=1);

namespace buzzingpixel\executive\commands;

use buzzingpixel\executive\services\CliQuestionService;
use EE_Config;
use EE_Lang;
use const PHP_EOL;
use function gettype;
use function print_r;

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
     */
    public function __construct(
        EE_Config $config,
        EE_Lang $lang,
        CliQuestionService $cliQuestionService
    ) {
        $this->config             = $config;
        $this->lang               = $lang;
        $this->cliQuestionService = $cliQuestionService;
    }

    /**
     * Get config item
     */
    public function get(?string $key, ?string $index) : void
    {
        if ($key === null) {
            $key = $this->cliQuestionService->ask(
                '<fg=cyan>' .
                $this->lang->line('configKey') .
                ': </>'
            );
        }

        $val = $this->config->item($key, $index ?? '');

        print_r('(' . gettype($val) . ') ');
        print_r($val);
        echo PHP_EOL;
    }
}
