Console Commands
================

Simplex provides support for easily creating console commands. This is built on top of the 
[Symfony Console Component](http://symfony.com/doc/current/components/console.html)

Run `$ bin/console` to see the list of available commands. The quickstart comes pre-baked with Doctrine ORM, including 
Doctrine console command integration.

Registering Commands
--------------------

Refer to the Symfony [documentation](http://symfony.com/doc/current/console.html) for guidance on creating commands.

One you have a command you'll need to register it inside one of your module's dependency injection 
[definition](http://php-di.org/doc/definition-overriding.html#arrays) files:

```php
'console_commands' => DI\add([
    DI\get(FooCommand::class),
]),
```
    

Helper Set
----------

If you need to register a helper set you can also do so using a dependency injection [definition](http://php-di.org/doc/definition-overriding.html#arrays).

```php
'console_helper_set' => function (ContainerInterface $c) {
    $entityManager = $c->get('orm');
    $ormHelperSet = ConsoleRunner::createHelperSet($entityManager);
    return $ormHelperSet;
},
```
