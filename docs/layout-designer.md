# Layout Designer

Migration classes have access to Executive's `LayoutDesigner` class. This class is designed to be user friendly and easy to use for anyone. It handles all the heavy lifting of interfacing with EE's models and services to create channel layouts.

```php
<?php

namespace User\Migration;

use BuzzingPixel\Executive\Abstracts\BaseMigration;

/**
 * Class m2017_09_04_161128_SchemaDesignerTesting
 */
class m2017_09_04_161128_SchemaDesignerTesting extends BaseMigration
{
    /**
     * Run migration
     * @throws \Exception
     */
    public function safeUp()
    {
        $this->layoutDesigner->channel('test')
            ->layoutName('Test Default Layout')
            ->addMemberGroup('Super Admin')
            ->addMemberGroup('My Custom Member Group')
            ->tab('Publish')
                ->addField('title')
                ->addField('url_title')
                ->addField('test_field_1')
                    ->fieldIsVisible(false)
                ->addField('test_field_2')
                    ->fieldIsCollapsed(true)
                ->addField('test_field_3')
                ->addField('entry_date')
            ->tab('Testing')
                ->addField('another_field')
                ->addField('some_field')
                ->addField('status')
                ->tabIsVisible(false)
            ->save();
    }
}
```

# Methods

Each of these methods except `save()` return an instance of the class.

### `siteName('custom_site')`

default = 'default_site'

For MSM sites, if you are manipulating schema for a site other than `default_site`, use this method to set the site name you will be manipulating.

### `channel('channel_name)`

Required.

Set the name of the channel the layout will apply to.

### `layoutName('My Custom Layout')`

Required.

Set the name of the layout to add or edit.

### `addMemberGroup('Super Admin')`

Add a member group that the layout will apply to.

### `removeMemberGroup('My Custom Group')`

Remove a member group from an existing layout.

### `tab('Tab Name')`

Add a tab and set it as the active tab to add fields to.

### `removeTab('Tab Name')`

Remove a tab from an existing layout. If the fields already in this tab are not re-assigned, they will be placed on the Publish tab.

### `tabIsVisible((bool))`

Set whether the active tab is visible. Note you cannot hide the Publish tab.

### `addField('field_name')`

Add a field to the currently active tab as the next field.

### `fieldIsVisible((bool))`

Set whether the currently active field is visible. Note you cannot hide a required field.

### `fieldIsCollapsed((bool))`

Sets whether the active fields default state is collapsed.

### `save()`

After using all the appropriate methods for the schema additions/changes, use the save method to commit all those changes.
