<?php

declare(strict_types=1);

namespace sample\name\space;

use buzzingpixel\executive\abstracts\MigrationAbstract;

/**
 * Note, if you want to do your own dependency injection, you can add this to
 * your diDefinitions config just like any other class and implement
 *
 * @see \buzzingpixel\executive\interfaces\MigrationInterface
 * Or you can still use the migration Abstract and send it appropriate
 * dependencies
 */


class Migration extends MigrationAbstract
{
    /**
     * Runs the migration
     */
    public function safeUp() : bool
    {
        // TODO: Update this method to run the migration
        return true;
    }

    /**
     * Reverses the migration
     */
    public function safeDown() : bool
    {
        // TODO: If the migration can be reversed, update this method
        return true;
    }
}
