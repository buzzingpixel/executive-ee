# Channel Designer

`MigrationAbstract` classes have access to Executive's `ChannelDesignerService`. This service is designed to be user friendly and easy to use for anyone. It handles all the heavy lifting of interfacing with EE's models and services to create or update all the things. Here's a brief example of the channel designer in use in a migration class.

```php
<?php
declare(strict_types=1);

namespace myapp\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;

/**
 * Class m2017_08_31_151615_ChannelDesignerTest
 */
class m2017_08_31_151615_ChannelDesignerTest extends MigrationAbstract
{
    /**
     * Runs the migration
     */
    public function safeUp(): bool
    {
        $this->channelDesignerFactory->make()
            ->addField(array(
                'field_name' => 'test_field_1',
                'field_label' => 'Test Field 1',
                'field_instructions' => 'This is a test field',
                'field_required' => true,
                'field_type' => 'text',
            ))
            ->addField(array(
                'field_name' => 'test_field_2',
                'field_label' => 'Test Field 2',
                'field_instructions' => 'This is another test field',
                'field_type' => 'text',
            ))
            ->channelName('my_test_channel')
            ->channelTitle('My Test Channel')
            ->save();
        
        return true;
    }
}
```

## Methods

Each of these methods can be used in conjunction with creating any or all of these items. For instance, if you just want to add new fields to a channel, or update existing fields, you can do that.

Each of the methods except `save()` return an instance of the class.

### `siteName('custom_site')`

For MSM sites, if you are manipulating schema for a site other than `default_site`, use this method to set the site name you will be manipulating.

### `addStatus('Custom Status', '009933')`

Add a status to the specified status group and optionally set the highlight color of that status. If you do not provide the second argument of `$color`, it will default to `'000000'`.

### `removeStatus('Custom Status')`

Removes a status from a channel. Note that this will not delete the status from the database as it may be used on other channels. If you wish to delete a status from the database, use the native model methods to do that.

### `addField(array())`

Set the field properties to add or update. The array argument can receive any property on ExpressionEngine's field model. Many third party fields will require specific settings to be added. The only required key for something like a test field is `field_name` although you'll probably want to set a `field_label` too. If you do not provide that, it will default to `field_name`.

### `removeField('field_name')`

Removes a field from a channel. Note that this will not delete the field from the database as it may be used on other channels. If you wish to delete a field from the database, use the native model methods to do that.

### `channelName('my_channel')`

Set the name of the channel to create or update.

### `channelTitle('My Channel')`

Set the title of the channel.

### `extendedChannelProperties(array())`

And array of any properties on the `Channel` model to set or update.

### `save()`

After using all the appropriate methods for the schema additions/changes, use the save method to commit all those changes.
