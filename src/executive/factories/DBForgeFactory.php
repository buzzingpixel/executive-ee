<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use CI_DB_mysqli_forge as DBForge;

class DBForgeFactory
{
    public function __construct()
    {
        ee()->load->dbforge();
    }

    /**
     * Gets the DB forge instance
     */
    public function make() : DBForge
    {
        return ee()->dbforge;
    }
}
