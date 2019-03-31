<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services;

use EllisLab\ExpressionEngine\Library\Filesystem\FilesystemException;
use Executive_upd;

class CliInstallService
{
    /** @var Executive_upd $executiveUpd */
    private $executiveUpd;

    /**
     * CliInstallService constructor
     */
    public function __construct(Executive_upd $executiveUpd)
    {
        $this->executiveUpd = $executiveUpd;
    }

    /**
     * Runs the installation
     *
     * @throws FilesystemException
     */
    public function run() : void
    {
        $this->executiveUpd->install();
    }
}
