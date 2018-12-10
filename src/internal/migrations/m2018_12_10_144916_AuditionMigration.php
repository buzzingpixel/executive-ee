<?php
declare(strict_types=1);

namespace buzzingpixel\executive\internal\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;
use EllisLab\ExpressionEngine\Model\Channel\Channel;

class m2018_12_10_144916_AuditionMigration extends MigrationAbstract
{
    public function safeUp(): bool
    {
        $fieldGroupCollection = $this->modelFacadeFactory->make()->get('ChannelFieldGroup')
            ->filter('group_name', 'IN', [
                'My First Field Group',
                'Another Field Group Title'
            ])
            ->all();

        /** @var Channel $channel */
        $channel = $this->modelFacadeFactory->make()->get('Channel')
            ->filter('channel_name', 'migration_test_channel')
            ->first();

        $channel->FieldGroups = $fieldGroupCollection;

        $channel->save();

        return true;
    }

    public function safeDown(): bool
    {
        echo "This migration cannot be reversed (because I'm not going to take the time to write it :))";
        return false;
    }
}
