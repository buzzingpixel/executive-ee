<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services;

use Executive_upd;
use EllisLab\ExpressionEngine\Library\Filesystem\FilesystemException;

/**
 * Class CliInstallService
 */
class CliInstallService
{
    /** @var Executive_upd $executiveUpd */
    private $executiveUpd;

    /**
     * CliInstallService constructor
     * @param Executive_upd $executiveUpd
     */
    public function __construct(Executive_upd $executiveUpd)
    {
        $this->executiveUpd = $executiveUpd;
    }

    /**
     * Runs the installation
     * @throws FilesystemException
     */
    public function run(): void
    {
        $this->executiveUpd->install();
    }
}
