<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services;

use buzzingpixel\executive\factories\CommandModelFactory;
use EE_Config;
use EllisLab\ExpressionEngine\Service\Addon\Addon;
use EllisLab\ExpressionEngine\Service\Addon\Factory as AddOnFactory;
use function array_filter;
use function array_map;
use function array_merge;
use function is_array;

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
     */
    public function __construct(
        EE_Config $config,
        AddOnFactory $addOnFactory,
        CommandModelFactory $commandModelFactory
    ) {
        $this->config              = $config;
        $this->addOnFactory        = $addOnFactory;
        $this->commandModelFactory = $commandModelFactory;
    }

    /**
     * Gets the available command groups
     */
    public function getCommandGroups() : array
    {
        $commandGroups = [];

        $userCommands = $this->config->item('commands');
        $userCommands = is_array($userCommands) ? $userCommands : [];

        if ($userCommands) {
            $commandGroups['user'] = $userCommands;
        }

        $providerCommands = array_map(
            static function (Addon $addOn) {
                $provider = $addOn->getProvider();

                $commands = $provider->get('commands');
                $commands = is_array($commands) ? $commands : null;

                if (! $commands) {
                    return false;
                }

                return $commands;
            },
            $this->addOnFactory->installed()
        );

        $providerCommands = array_filter($providerCommands, static function ($i) {
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
