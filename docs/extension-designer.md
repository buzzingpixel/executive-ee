# Extension Designer

Migration classes have access to Executive's `ExtensionDesigner` class. This class is designed to make it very easy for anyone to add a custom extension for your project without needing to write out a full add-on. here's a full example of how to use `ExtensionDesigner`.

```php
<?php

namespace User\Migration;

use BuzzingPixel\Executive\Abstracts\BaseMigration;

/**
 * Class m2017_09_06_095807_TestingExtensionDesigner
 */
class m2017_09_06_095807_TestingExtensionDesigner extends BaseMigration
{
    /**
     * Run migration
     * @throws \Exception
     */
    public function safeUp()
    {
        $this->extensionDesigner->extClass('\User\Extension\TestExtension')
            ->extMethod('testMethod')
            ->extHook('before_channel_entry_update')
            ->extPriority(1) // Not required, defaults to 10
            ->add(); // You can also call the `remove()` method if you are removing an extension.
    }
}

```
