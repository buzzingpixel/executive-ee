# User Class Autoloading

Executive includes an auto-loader that loads classes in the `system/user` directory. The namespace of the class should start with `User`, and the rest of the namespace should correspond to the directory structure the class file is in. So for instance if you had a file as follows:

```
system/user/Service/MyCoolService.php
```

Your class in `MyCoolService.php` should look like this:

```php
<?php

namespace User\Service;

class MyCoolService
{
    public function myCoolMethod()
    {
        // Do stuff...
    }
}
```
