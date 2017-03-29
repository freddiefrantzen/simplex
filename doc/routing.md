Routing
=======

Simplex uses the [Symfony Router](http://symfony.com/doc/current/components/routing.html). Presently you must use
annotations to define routes (php route builder is on the roadmap).


Defining Routes using Annotations
---------------------------------

For the purpose of extracting route annotations, Simplex will scan all classes inside each module's `Controller` 
directory. 

Refer to the Symfony [documentation](http://symfony.com/doc/current/routing.html) for details about how to
define the route annotations.


Generating URLs
---------------

A url generator is available for convenience. This is injected into the core, base controller and is also available
as a service from the DI container. Refer to the Symfony [documentation](http://symfony.com/doc/current/routing.html#generating-urls) 
for details about how to generate urls.
