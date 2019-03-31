<?php

declare(strict_types=1);

use buzzingpixel\executive\commands\ComposerProvisionCommand;
use buzzingpixel\executive\ExecutiveDi;
use Symfony\Component\Dotenv\Dotenv;
use Whoops\Handler\PrettyPageHandler as WhoopsPrettyPageHandler;
use Whoops\Run as WhoopsRunner;

// Set up composer
$sep            = DIRECTORY_SEPARATOR;
$basePath       = __DIR__;
$vendorAutoload = $basePath . $sep . 'vendor' . $sep . 'autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

// Load up Dotenv so it will be available
$dotEnvFile = $basePath . $sep . '.env';
if (class_exists(Dotenv::class) && file_exists($dotEnvFile)) {
    (new Dotenv())->load($dotEnvFile);
}

// Define constants
define('APP_DIR', __DIR__);
define('SYSPATH', $basePath . $sep . 'system' . $sep);
define('SYSDIR', basename(SYSPATH));
define('DEBUG', getenv('DEV_MODE') === 'true' ? 1 : 0);

// To run the EE installer and/or perform updates, set to true
$installMode = getenv('EE_INSTALL_MODE');
define('INSTALL_MODE', $installMode === 'true' || $installMode === 'TRUE');

// Set up debugging
$query         = [];
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
            ExecutiveDi::diContainer()->get(ComposerProvisionCommand::class)->run();
            exit;
        } catch (Throwable $e) {
            exit(
                "\033[31m" . $e->getMessage() . "\n" .
                'File: ' . $e->getFile() . "\n" .
                'Line: ' . $e->getLine() . "\n"
            );
        }
    }
}

if (! file_exists($sysPath)) {
    // @codingStandardsIgnoreStart
    $fileName = SELF;
    // @codingStandardsIgnoreEnd

    if (PHP_SAPI === 'cli') {
        exit(
            "\033[31mYour system folder path does not appear to be set correctly.\n" .
            "\033[31mDo you need to run \"php " . $fileName . " executive composerProvision\"?.\n"
        );
    }

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    exit('Your system folder path does not appear to be set correctly');
}

require_once $sysPath;
