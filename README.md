[![Build Status](https://scrutinizer-ci.com/g/freddiefrantzen/simplex/badges/build.png?b=master)](https://scrutinizer-ci.com/g/freddiefrantzen/simplex/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/freddiefrantzen/simplex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/freddiefrantzen/simplex/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/freddiefrantzen/simplex/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/freddiefrantzen/simplex/?branch=master)

Simplex Framework
=================

**Note: This is a work-in-progress and the APIs will *very likely* change without warning**


What is Simplex?
----------------

A modular, micro framework build on top of the [PHP-DI](http://php-di.org/) dependency injection container and the 
[Symfony Router](http://symfony.com/doc/current/components/routing.html). 

* Lightweight
* Modular
* Code and convention over excessive configuration
* Compatible with [PSR-7](http://www.php-fig.org/psr/psr-7/) middleware 
* Out-of-box support for console commands
* Controllers as first-class citizens
* Supports route annotations
* Built-in caching
* [Doctrine ORM](http://www.doctrine-project.org/projects/orm.html) integration (can be replaced with ORM of your choice)
* [Hateoas serializer](https://github.com/willdurand/Hateoas) integration 


Installation
------------

Install with composer

    $ composer create-project simplex/quickstart -s dev
    
Once installed, create a `.env` file in the root directory (see the `.env.dist` template).

The quickstart demo requires a Mysql database. This can be created by running the following command

    $ composer init-db

Now start a webserver
    
    $ php -S 0.0.0.0:8080 -t public public/app.php
    
And point your browser to `http://localhost:8080/`


Documentation
-------------

* [Modules](doc/modules.md)
* [Environments and Configuration](doc/envs-and-config.md)
* [Controllers](doc/controllers.md)
* [Routing](doc/routing.md)
* [Http Middleware](doc/http-middleware.md)
* [Caching](doc/caching.md)
* [Console Commands](doc/console-commands.md)

See the [quickstart](https://github.com/freddiefrantzen/simplex-quickstart) for a demo module as well as 
[Factories](http://php-di.org/doc/php-definitions.html#factories) for Doctrine ORM and Hateoas serializer.


Roadmap
-------

* Provide support for defining routes in code











