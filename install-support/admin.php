<?php

/**
 * Set path to the EEFrontController.php file
 * This example assumes that EEFrontController.php is one level above web root
 * (and for safety and security, it should be)
 */
$frontControllerPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'EEFrontController.php';

/**
 * Probably don't edit anything below this
 */

define('MASKED_CP', true);
define('REQ', 'CP');
define('SELF', basename(__FILE__));
define('FCPATH', __DIR__ . '/');
require $frontControllerPath;
