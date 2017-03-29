Modules
=======

Essentially, a module is simply a path to a code component containing some dependency injection 
[definitions](http://php-di.org/doc/php-definitions.html). 

You can define whatever directory structure best fits your needs. The only stipulation is that a module contains a 
`config` directory inside of which you include zero or more files containing the dependency injection definitions. See 
[php-di](http://php-di.org/doc/php-definitions.html) for details of how to wire up your dependencies..

Any modules under `src/Modules` will be auto-discovered. You can include modules in other locations by specifying an array
of paths to root module directories inside a top-level module file `config/modules.php`. For example:

```php
<?php

// /modules.php

return [
    __DIR__ . '/path/to/some/module',
    __DIR__ . '/path/to/some/other/module',            
];
```
