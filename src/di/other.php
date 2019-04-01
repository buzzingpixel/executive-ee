<?php

declare(strict_types=1);

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
];
