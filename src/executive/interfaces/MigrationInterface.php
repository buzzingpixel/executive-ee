<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\interfaces;

/**
 * Interface MigrationInterface
 */
interface MigrationInterface
{
    /**
     * Runs the migration
     * As the "safe" part of the name indicates, this method should never
     * throw errors and should return a boolean of false if something fails
     * @return bool
     */
    public function safeUp(): bool;

    /**
     * Reverses the migration
     * As the "safe" part of the name indicates, this method should never
     * throw errors and should return a boolean of false if something fails
     * or if the migration cannot be reversed
     * @return bool
     */
    public function safeDown(): bool;
}
