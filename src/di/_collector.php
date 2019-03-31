<?php

declare(strict_types=1);

$directory = new RecursiveDirectoryIterator(__DIR__);

$iterator = new RecursiveIteratorIterator($directory);

$finalIterator = new RegexIterator(
    $iterator,
    '/^.+\.php$/i',
    RecursiveRegexIterator::GET_MATCH
);

$config = $collect = [];

foreach ($finalIterator as $files) {
    foreach ($files as $file) {
        if (pathinfo($file)['basename'] === '_collector.php') {
            continue;
        }

        $conf = include $file;

        if (! is_array($conf)) {
            continue;
        }

        $collect[] = $conf;
    }
}

foreach ($collect as $item) {
    foreach ($item as $key => $val) {
        $config[$key] = $val;
    }
}

return $config;
