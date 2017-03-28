Simplex Framework
=================

**Note: This is a work-in-progress and the APIs will *very likely* change without warning**


What is Simplex?
----------------

A modular, micro framework build on top of the [PHP-DI](http://php-di.org/) dependency injection container and the 
[Symfony router](http://symfony.com/doc/current/components/routing.html). 

Some key benefits:

* Lightweight
* Modular
* Code and convention over excessive configuration
* Compatible with [PSR7](http://www.php-fig.org/psr/psr-7/) middleware 
* Out-of-box support for console commands
* Controllers as first-class citizens
* Supports route annotations
* Built-in caching
* [Doctrine ORM](http://www.doctrine-project.org/projects/orm.html) integration (can be replaced with ORM of your choice)
* [HATEOAS](https://github.com/willdurand/Hateoas) serializer integration 


Installation
------------

Install with composer `composer create-project simplex/quickstart`.


Documentation
-------------

* [Modules](doc/modules.md)
* [Environments and Configuration](doc/envs-and-config.md)
* [Controllers](doc/controllers.md)
* [Routing](doc/routing.md)
* [Http Middleware](doc/http-middleware.md)
* [Caching](doc/caching.md)
* [Console Commands](doc/console-commands.md)


Roadmap
-------

* Provide support for defining routes in code











