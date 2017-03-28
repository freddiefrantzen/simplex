Modules
=======

Essentially, a module is simply a path to a code component containing some dependency injection [definitions]((http://php-di.org/)). 

You can define whatever directory structure best fits your needs. The only stipulation are that a module contains a 
`config` directory inside of which you include zero or more files containing dependency injection definitions. See 
[php-di](http://php-di.org/) for details of how to define these definitions.

Any modules under `src/Modules` will be auto-discovered. You can include modules in other locations by specifying the
paths inside a top-level module file `config/modules.php`. This file should return an array of paths to the root module
directories.
