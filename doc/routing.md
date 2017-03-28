Routing
=======

Simplex uses the [Symfony router](http://symfony.com/doc/current/components/routing.html). Presently you must use
annotations to define routes (php route builder is on the roadmap).


Defining Routes using Annotations
---------------------------------

Refer to the Symfony [documentation](http://symfony.com/doc/current/routing.html).


Generating URLs
---------------

A url generator is available for convenience. This is injected into the core, base controller and is also available
as a service from the DI container. Refer to the Symfony [documentation](http://symfony.com/doc/current/routing.html#generating-urls). 
