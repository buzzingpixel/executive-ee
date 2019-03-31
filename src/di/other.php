<?php

declare(strict_types=1);

use buzzingpixel\executive\factories\TwigFactory;
use Twig\Environment as TwigEnvironment;

return [
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
    TwigEnvironment::class => static function () {
        return (new TwigFactory())->get();
    },
];
