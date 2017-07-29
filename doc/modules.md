Modules
=======

A module is simply a code component containing a collection of logically grouped classes. You are free to define 
whatever directory structure best fits your needs. 

A module must contain a `Module` class in the root module directory which must implement `Simplex\Module`. There is one
method to implement, `getServiceConfigPath`. This should return a path to a directory inside of which you include zero 
or more files containing the dependency injection definitions for you module, or `null` if your module does not contain 
any definitions. See [php-di](http://php-di.org/doc/php-definitions.html) for details of how to wire up your 
dependencies.

You will find a [demo module](https://github.com/freddiefrantzen/simplex-quickstart/tree/master/src/Module/Demo) in the 
quickstart.

A new instance of each module should be added to the modules array in the root modules file.

```php
<?php

// /config/modules.php

return [
    new AppModule(),
];
```
