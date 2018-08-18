<?php
declare(strict_types=1);

use buzzingpixel\executive\commands\ComposerProvisionCommand;

return [
    /**
     * Commands
     */
    ComposerProvisionCommand::class => function () {
        return new ComposerProvisionCommand();
    },
];
