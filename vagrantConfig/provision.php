<?php

// Get paths
$path = realpath(__DIR__);
$jsonConfigPath = "{$path}/config.json";
$jsonConfigLocalPath = "{$path}/configLocal.json";
$siteConfPath = "{$path}/siteConfigs/*";

// Iterate through site configs and symlink them into place
foreach (glob($siteConfPath) as $siteConfig) {
    // Get the path info
    $pathInfo = pathinfo($siteConfig);

    // Symlink the config into place
    exec("ln -s /vagrant/vagrantConfig/siteConfigs/{$pathInfo['basename']} /etc/nginx/sites-enabled/{$pathInfo['basename']}");
}

// Get configs
$jsonConfig = json_decode(file_get_contents($jsonConfigPath), true);
if (file_exists($jsonConfigLocalPath)) {
    $jsonConfigLocal = json_decode(file_get_contents($jsonConfigLocalPath), true);
    $jsonConfig = array_merge($jsonConfig, $jsonConfigLocal);
}

// Configure databases
if (array_key_exists('databases', $jsonConfig)) {
    foreach ($jsonConfig['databases'] as $databaseConfig) {
        // Create the database if it does not exist
        exec('mysql -uroot -psecret -e "CREATE DATABASE IF NOT EXISTS ' . $databaseConfig['name'] . ';"');

        // Create the database user
        exec('mysql -uroot -psecret -e "CREATE USER IF NOT EXISTS \'' . $databaseConfig['user'] . '\'@\'localhost\' IDENTIFIED BY \'' . $databaseConfig['password'] . '\'"');

        // Grant database user permissions
        exec('mysql -uroot -psecret -e "GRANT ALL on ' . $databaseConfig['name'] . '.* to \'' . $databaseConfig['user'] . '\'@\'localhost\'"');

        // Import the most recent database dump
        if (file_exists("/vagrant/localStorage/dbBackups/{$databaseConfig['name']}_latest.sql")) {
            exec("mysql -uroot -psecret {$databaseConfig['name']} < /vagrant/localStorage/dbBackups/{$databaseConfig['name']}_latest.sql");
        }
    }
}
