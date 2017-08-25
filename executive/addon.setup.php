<?php // @codingStandardsIgnoreStart

// @codingStandardsIgnoreEnd

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/construct/license
 * @link https://buzzingpixel.com/software/construct
 */

// Get addon json path
$addOnPath = realpath(__DIR__);
$addOnJsonPath = "{$addOnPath}/addon.json";

// Get the addon json file
$addOnJson = json_decode(file_get_contents($addOnJsonPath));

// Set paths
$sysPath = rtrim(realpath(SYSPATH), '/') . '/';
$cachePath = "{$sysPath}user/cache/";

// Get vendor autoload
$vendorAutoloadFile = "{$addOnPath}/vendor/autoload.php";

// Require the autoload file if path exists
if (file_exists($vendorAutoloadFile)) {
    require $vendorAutoloadFile;
}

// Define constants
defined('EXECUTIVE_NAME') || define('EXECUTIVE_NAME', $addOnJson->label);
defined('EXECUTIVE_VER') || define('EXECUTIVE_VER', $addOnJson->version);

// Check if this is a console request
if (defined('REQ') && REQ === 'CONSOLE') {
    // Make sure lang file is loaded
    ee()->lang->loadfile('executive');

    // Run query to check if Executive is installed
    /** @var \EllisLab\ExpressionEngine\Service\Database\Query $queryBuilder */
    $queryBuilder = ee('db');
    $query = (int) $queryBuilder->where('module_name', 'Executive')
        ->get('modules')
        ->num_rows();

    // If Executive is not installed
    if ($query < 1) {
        // Get arguments sent to command line
        $args = EXECUTIVE_RAW_ARGS;

        // If command line is not requesting an installation, warn user
        if (isset($args[2]) ||
            count($args) > 2 ||
            ! isset($args[1]) ||
            $args[1] !== 'install'
        ) {
            $lang = lang('notInstalled');
            exit("\033[31m{$lang}\n");
        }

        // Get the executive installer
        include_once __DIR__ . '/upd.executive.php';
        $installer = new Executive_upd();

        // Run the installer
        $installer->install();
    }
}

// Return info about the add on for ExpressionEngine
return array(
    'author' => $addOnJson->author,
    'author_url' => $addOnJson->authorUrl,
    'description' => $addOnJson->description,
    'docs_url' => $addOnJson->docsUrl,
    'name' => $addOnJson->label,
    'namespace' => $addOnJson->namespace,
    'settings_exist' => $addOnJson->settingsExist,
    'version' => $addOnJson->version,
    'services' => array(),
);
