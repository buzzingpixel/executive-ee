<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services;

use EE_Config;
use EllisLab\ExpressionEngine\Service\Addon\Addon;
use buzzingpixel\executive\factories\CommandModelFactory;
use EllisLab\ExpressionEngine\Service\Addon\Factory as AddOnFactory;

/**
 * Class CommandsService
 */
class CommandsService
{
    /** @var EE_Config $config */
    private $config;

    /** @var AddOnFactory $addOnFactory */
    private $addOnFactory;

    /** @var CommandModelFactory $commandModelFactory */
    private $commandModelFactory;

    /**
     * CommandsService constructor
     * @param EE_Config $config
     * @param AddOnFactory $addOnFactory
     * @param CommandModelFactory $commandModelFactory
     */
    public function __construct(
        EE_Config $config,
        AddOnFactory $addOnFactory,
        CommandModelFactory $commandModelFactory
    ) {
        $this->config = $config;
        $this->addOnFactory = $addOnFactory;
        $this->commandModelFactory = $commandModelFactory;
    }

    /**
     * Gets the available command groups
     * @return array
     */
    public function getCommandGroups(): array
    {
        $commandGroups = [];

        $userCommands = $this->config->item('commands');
        $userCommands = \is_array($userCommands) ? $userCommands : [];

        if ($userCommands) {
            $commandGroups['user'] = $userCommands;
        }

        $providerCommands = array_map(
            function (Addon $addOn) {
                $provider = $addOn->getProvider();

                $commands = $provider->get('commands');
                $commands = \is_array($commands) ? $commands : null;

                if (! $commands) {
                    return false;
                }

                return $commands;
            },
            $this->addOnFactory->installed()
        );

        $providerCommands = array_filter($providerCommands, function ($i) {
            return $i !== false;
        });

        $commandGroups = array_merge($commandGroups, $providerCommands);

        $returnCommandGroups = [];

        foreach ($commandGroups as $groupName => $group) {
            foreach ($group as $cmd => $val) {
                $model = $this->commandModelFactory->make();

                $model->setPropertiesFromArray($val);

                $model->setName($cmd);

                $returnCommandGroups[$groupName][$cmd] = $model;
            }
        }

        return $returnCommandGroups;
    }
}
