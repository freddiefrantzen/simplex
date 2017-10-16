Controllers
===========

Creating Controllers
--------------------

Simplex ships with a base controller which provides access to the serializer and url generator as well as providing
some convenience methods for generating responses. You controllers can optionally extent this base controller, you can 
subclass the base controller (see below), create your own base controller or you may choose to create controllers 
without inheritance :) 

Here's an example controller which extends the core, base controller.
```php
<?php

namespace Frantzen\Framework\App\Module\Demo\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Simplex\BaseController;
use Symfony\Component\Routing\Annotation\Route;

class DemoController extends BaseController
{
    /**
     * @Route("/{name}", methods={"GET"}, name="hello")
     */
    public function index(string $name): Response
    {
        return $this->jsonResponse(['hello' => $name]);
    }
}
```

If you need to access request attributes directly you may (optionally) specify the first controller action argument as 
of type `Psr\Http\Message\ServerRequestInterface`. This should be followed by the arguments as defined in the route 
definition.

Controller actions must return a response of type `Psr\Http\Message\ResponseInterface` . The base controller contains 
helper methods to simplify creating responses.


Extending or Replacing the Base Controller
------------------------------------------

You can extend or replace the base controller to provide additional dependencies. You must use setter injection. 
For example:

```php
<?php

class MyBaseController
{
    private $foo;
    
    public function setFoo(Foo $foo)
    {
        $this->foo = $foo;
    }
}
```

You will then need to define a dependency injection [definition](http://php-di.org/doc/definition-overriding.html#arrays) 
for `MyBaseController` dependencies.

```php

'controller_dependencies' => DI\add(
    [
        MyBaseController::class => [
            'foo' => DI\get(Foo::class),
        ]
    ]
),

```

Now `setFoo` will be called and passed an instance of `Foo` each time a controller that extends from `MyBaseController`
is matched by the router. You can create as many base controllers as you wish.
