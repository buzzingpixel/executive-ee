<?php

declare(strict_types=1);

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

return [
    HelperInterface::class => static function () {
        return (new ConsoleApplication())
            ->getHelperSet()
            ->get('question');
    },
    InputInterface::class => static function () {
        return new ArgvInput();
    },
    OutputInterface::class => static function () {
        return new ConsoleOutput();
    },
    'eeSiteShortNames' => static function () {
        $sites = ee('Model')->get('Site')
            ->fields('site_name')
            ->all();

        $sitesArray = [];

        foreach ($sites as $site) {
            $sitesArray[$site->site_id] = $site->site_name;
        }

        return $sitesArray;
    },
];
