<?php

// Get paths
$path = realpath(__DIR__);
$jsonConfigPath = "{$path}/config.json";
$jsonConfigLocalPath = "{$path}/configLocal.json";
$siteConfPath = "{$path}/siteConfigs/*";

// Get configs
$jsonConfig = json_decode(file_get_contents($jsonConfigPath), true);
if (file_exists($jsonConfigLocalPath)) {
    $jsonConfigLocal = json_decode(file_get_contents($jsonConfigLocalPath), true);
    $jsonConfig = array_merge($jsonConfig, $jsonConfigLocal);
}

// Iterate through databases
if (array_key_exists('databases', $jsonConfig)) {
    foreach ($jsonConfig['databases'] as $databaseConfig) {
        // Dump the database
        exec("mysqldump -u{$databaseConfig['user']} -p{$databaseConfig['password']} {$databaseConfig['name']} > /vagrant/localStorage/dbBackups/{$databaseConfig['name']}_latest_new.sql");

        // Delete the previous DB dump
        if (file_exists("/vagrant/localStorage/dbBackups/{$databaseConfig['name']}_previous.sql")) {
            exec("rm /vagrant/localStorage/dbBackups/{$databaseConfig['name']}_previous.sql");
        }

        // Rename latest DB dump to previous DB dump
        if (file_exists("/vagrant/localStorage/dbBackups/{$databaseConfig['name']}_latest.sql")) {
            exec("mv /vagrant/localStorage/dbBackups/{$databaseConfig['name']}_latest.sql /vagrant/localStorage/dbBackups/{$databaseConfig['name']}_previous.sql");
        }

        // Rename the new DB dump to the latest DB dump
        exec("mv /vagrant/localStorage/dbBackups/{$databaseConfig['name']}_latest_new.sql /vagrant/localStorage/dbBackups/{$databaseConfig['name']}_latest.sql");
    }
}
