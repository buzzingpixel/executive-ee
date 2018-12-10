# Channels and Fields Migration Examples

## Add a channel and some fields

One trick to knowing what to add to the `set()` array when calling `$fieldModel->set(['somedata' => 'someValue'])` is, when writing your migration in your development environment, `var_export` the `$_POST` data at the top of your `admin.php` file (don't forget to remove that code before `git commit`ting) and set up all your field or channel settings, then hit save. You'll get a look at everything that needs to be set on the model before saving. Here's an example for placing temporarily at the top of your `admin.php` file.

```php
<?php

if ($_POST) {
    echo '<pre>';
    var_export($_POST);
    die;
}
```

## Adding new fields and new channel

```php
<?php
declare(strict_types=1);

namespace buzzingpixel\executive\internal\migrations;

use EllisLab\ExpressionEngine\Model\Channel\Channel;
use buzzingpixel\executive\abstracts\MigrationAbstract;
use EllisLab\ExpressionEngine\Model\Channel\ChannelField;

class m2018_12_10_144914_AuditionMigration extends MigrationAbstract
{
    public function safeUp(): bool
    {
        $statusesCollection = $this->modelFacadeFactory->make()->get('Status')
            ->filter('status', 'IN', ['open', 'closed'])
            ->all();

        $fieldCollection = $this->modelCollectionFactory->make([
            $this->createTestTextFieldModel(),
            $this->createTestGridField()
        ]);

        $fieldCollection->save();

        /** @var Channel $channelModel */
        $channelModel = $this->modelFacadeFactory->make()->make('Channel');

        $channelModel->set([
            'channel_title' => 'Migration Test Channel',
            'channel_name' => 'migration_test_channel',
            'max_entries' => 0,
            'title_field_label' => 'Title',
            'channel_description' => 'My Test Channel',
            'channel_lang' => 'en',
            'channel_url' => '',
            'comment_url' => '',
            'search_results_url' => '',
            'rss_url' => '',
            'preview_url' => '',
            'default_entry_title' => '',
            'url_title_prefix' => '',
            'deft_status' => 'open',
            'deft_category' => '',
            'channel_html_formatting' => 'all',
            'extra_publish_controls' => 'n',
            'channel_allow_img_urls' => 'y',
            'channel_auto_link_urls' => 'n',
            'default_status' => '',
            'default_author' => '1',
            'allow_guest_posts' => 'n',
            'enable_versioning' => 'n',
            'max_revisions' => '10',
            'comment_notify_authors' => 'n',
            'channel_notify' => 'n',
            'channel_notify_emails' => '',
            'comment_notify' => 'n',
            'comment_notify_emails' => '',
            'comment_system_enabled' => 'y',
            'deft_comments' => 'y',
            'comment_require_membership' => 'n',
            'comment_require_email' => 'y',
            'comment_moderate' => 'n',
            'comment_max_chars' => '5000',
            'comment_timelock' => '0',
            'comment_expiration' => 0,
            'comment_text_formatting' => 'xhtml',
            'comment_html_formatting' => 'safe',
            'comment_allow_img_urls' => 'n',
            'comment_auto_link_urls' => 'y',
        ]);

        $channelModel->CustomFields = $fieldCollection;

        $channelModel->Statuses = $statusesCollection;

        $channelModel->save();

        return true;
    }

    public function safeDown(): bool
    {
        echo "This migration cannot be reversed (because I'm not going to take the time to write it :))";
        return false;
    }

    private function createTestTextFieldModel(): ChannelField
    {
        $modelFacade = $this->modelFacadeFactory->make();

        /** @var ChannelField $field */
        $field = $modelFacade->make('ChannelField');

        $field->set([
            'field_list_items' => '', // Required because of schema weirdness in EE
            'field_order' => '', // Required because of schema weirdness in EE
            'field_type' => 'text',
            'field_label' => 'Test Text Field',
            'field_name' => 'test_text_field',
            'field_instructions' => 'Enter text in this field',
            'field_required' => 'n',
            'field_search' => 'y',
            'field_is_hidden' => 'n',
            'field_maxl' => '256',
            'field_fmt' => 'none',
            'field_show_fmt' => 'n',
            'field_text_direction' => 'ltr',
            'field_content_type' => 'all',
        ]);

        return $field;
    }

    private function createTestGridField(): ChannelField
    {
        $modelFacade = $this->modelFacadeFactory->make();

        /** @var ChannelField $field */
        $field = $modelFacade->make('ChannelField');

        $field->set([
            'field_list_items' => '', // Required because of schema weirdness in EE
            'field_order' => '', // Required because of schema weirdness in EE
            'field_type' => 'grid',
            'field_label' => 'Test Grid Field',
            'field_name' => 'test_grid_field',
            'field_instructions' => '',
            'field_required' => 'n',
            'field_search' => 'n',
            'field_is_hidden' => 'n',
            'grid_min_rows' => '0',
            'grid_max_rows' => '',
            'allow_reorder' => 'y',
            'grid' => [
                'cols' => [
                    'new_0' => [
                        'col_type' => 'radio',
                        'col_label' => 'Test Grid Radio Buttons',
                        'col_name' => 'test_grid_radio_buttons',
                        'col_instructions' => '',
                        'col_required' => 'n',
                        'col_search' => 'n',
                        'col_width' => '',
                        'col_settings' => [
                            'field_fmt' => 'none',
                            'field_pre_populate' => 'v',
                            'value_label_pairs' => [
                                'rows' => [
                                    'new_row_1' => [
                                        'value' => 'val_1',
                                        'label' => 'Val 1',
                                    ],
                                    'new_row_2' => [
                                        'value' => 'val_2',
                                        'label' => 'Val 2',
                                    ],
                                ],
                            ],
                            'field_list_items' => '',
                        ],
                    ],
                    'new_1' => [
                        'col_type' => 'textarea',
                        'col_label' => 'Test Grid Text Area',
                        'col_name' => 'test_grid_text_area',
                        'col_instructions' => '',
                        'col_required' => 'n',
                        'col_search' => 'n',
                        'col_width' => '',
                        'col_settings' => [
                            'field_ta_rows' => '6',
                            'field_fmt' => 'none',
                            'field_text_direction' => 'ltr',
                        ],
                    ],
                ],
            ],
        ]);

        return $field;
    }
}
```

## Adding a field group

```php
<?php
declare(strict_types=1);

namespace buzzingpixel\executive\internal\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;
use EllisLab\ExpressionEngine\Model\Channel\ChannelFieldGroup;

class m2018_12_10_144915_AuditionMigration extends MigrationAbstract
{
    public function safeUp(): bool
    {
        $fieldsCollection = $this->modelFacadeFactory->make()->get('ChannelField')
            ->filter('field_name', 'IN', [
                'test_grid_field',
                'test_text_field',
            ])
            ->all();

        /** @var ChannelFieldGroup $fieldGroup */
        $fieldGroup = $this->modelFacadeFactory->make()->make('ChannelFieldGroup');

        $fieldGroup->setProperty('group_name', 'My First Field Group');

        $fieldGroup->ChannelFields = $fieldsCollection;

        $fieldGroup->save();

        return true;
    }

    public function safeDown(): bool
    {
        echo "This migration cannot be reversed (because I'm not going to take the time to write it :))";
        return false;
    }
}
```

## Adding a field group to a channel

```php
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
```
