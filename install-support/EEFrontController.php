<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use Whoops\Run as WhoopsRunner;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\commands\ComposerProvisionCommand;
use Whoops\Handler\PrettyPageHandler as WhoopsPrettyPageHandler;

// Set up composer
$sep = DIRECTORY_SEPARATOR;
$basePath = __DIR__;
$vendorAutoload = $basePath . $sep . 'vendor' . $sep . 'autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

// Load up Dotenv so it will be available
if (class_exists(Dotenv::class) && file_exists($basePath . $sep . '.env')) {
    (new Dotenv($basePath))->load();
}

// Define constants
define('SYSPATH', $basePath . $sep . 'system' . $sep);
define('SYSDIR', basename(SYSPATH));
define('DEBUG', getenv('DEV_MODE') === 'true' ? 1 : 0);

// Set up debugging
$query = [];
$isCpJsRequest = false;

if (! empty($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $query);
}

$isCpJsRequest = isset($query['C']) && $query['C'] === 'javascript';

if ((! $isCpJsRequest && DEBUG === 1) || PHP_SAPI === 'cli') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    if (PHP_SAPI !== 'cli' &&
        class_exists(WhoopsRunner::class) &&
        class_exists(WhoopsPrettyPageHandler::class)
    ) {
        $whoops = new WhoopsRunner();
        $whoops->pushHandler(new WhoopsPrettyPageHandler());
        $whoops->register();
    }
} else {
    error_reporting(0);
}

$sysPath = SYSPATH . 'ee' . $sep . 'EllisLab' . $sep . 'ExpressionEngine' .
    $sep . 'Boot' . $sep . 'boot.php';

if (PHP_SAPI === 'cli' && defined('EXECUTIVE_RAW_ARGS')) {
    $executiveArg = EXECUTIVE_RAW_ARGS[1] ?? '';
    $provisionArg = EXECUTIVE_RAW_ARGS[2] ?? '';

    if ($executiveArg === 'executive' &&
        $provisionArg === 'composerProvision'
    ) {
        try {
            ExecutiveDi::get(ComposerProvisionCommand::class)->run();
            exit();
        } catch (Exception $e) {
            exit("\033[31m" . $e->getMessage() . "\n");
        }
    }
}

if (! file_exists($sysPath)) {
    if (PHP_SAPI === 'cli') {
        exit(
            "\033[31mYour system folder path does not appear to be set correctly.\n" .
            "\033[31mDo you need to run \"php " . SELF  . " executive composerProvision\"?.\n"
        );
    }

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    exit('Your system folder path does not appear to be set correctly');
}

require_once $sysPath;
