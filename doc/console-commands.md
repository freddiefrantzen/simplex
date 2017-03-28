Console Commands
================

Simplex provides support for easily creating console commands. This is built on top of the Symfony 
[console component](http://symfony.com/doc/current/components/console.html)


Registering Commands
--------------------

Refer to the Symfony [documentation](http://symfony.com/doc/current/console.html) for guidance on creating commands.

One you have a command you'll need to register it inside a module dependency injection [definition]((http://php-di.org/) ) file:

```php
'console_commands' => DI\add([
    DI\get(FooCommand::class),
]),
```
    

Helper Set
----------

If you need to register a helper set you can also do so using a dependency injection [definition]((http://php-di.org/) ).

```php
'console_helper_set' => function (ContainerInterface $c) {
    $entityManager = $c->get('orm');
    $ormHelperSet = ConsoleRunner::createHelperSet($entityManager);
    return $ormHelperSet;
},

```
