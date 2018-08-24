<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\factories;

use CI_DB_mysqli_forge as DBForge;

/**
 * Class Service
 */
class DBForgeFactory
{
    public function __construct()
    {
        ee()->load->dbforge();
    }

    /**
     * Gets the DB forge instance
     * @return DBForge
     */
    public function make(): DBForge
    {
        return ee()->dbforge;
    }
}
