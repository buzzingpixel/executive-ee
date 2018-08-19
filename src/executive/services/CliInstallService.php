<?php
declare(strict_types=1);

namespace buzzingpixel\executive\services;

use Executive_upd;

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
     */
    public function run(): void
    {
        $this->executiveUpd->install();
    }
}
