# Extension Designer

Migration classes have access to Executive's `ExtensionDesignerService`. This service is designed to make it very easy for anyone to add a custom extension for your project without needing to write out a full add-on. Here's a full example of how to use `ExtensionDesigner`.

```php
<?php
declare(strict_types=1);

namespace myapp\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;

/**
 * Class m2017_09_06_095807_TestingExtensionDesigner
 */
class m2017_09_06_095807_TestingExtensionDesigner extends MigrationAbstract
{
    /**
         * Runs the migration
         */
    public function safeUp()
    {
        $this->extensionDesignerFactory->make()
            ->extClass('\User\Extension\TestExtension')
            ->extMethod('testMethod')
            ->extHook('before_channel_entry_update')
            ->extPriority(1) // Not required, defaults to 10
            ->add(); // You can also call the `remove()` method if you are removing an extension.
    }
}
```
