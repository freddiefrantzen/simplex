Controllers
===========

Creating Controllers
--------------------

Simplex ships with a base controller which provides access to the serializer and url generator as well as providing
some convenience methods for generating responses. You controller's can optionally extent this base controller, you can 
subclass the base controller (see below), create your own base controller or you may choose to create controllers 
without inheritance :)

Here's an example controller, which extends the core, base controller.
```php
<?php

namespace Frantzen\Framework\App\Module\Demo\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Frantzen\Framework\Core\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class DemoController extends Controller
{
    /**
     * @Route("/{name}", methods={"GET"}, name="hello")
     */
    public function index(Request $request, Response $response, string $name): Response
    {
        return $this->jsonResponse($response, ['hello' => $name]);
    }
}
```

The first argument for all controller actions will be the `Request`, the second will be the `Response`, followed by
the arguments as defined in the route definition.

Controller actions must return a response. The base controller contains helper actions to simplify returning responses.


Extending the Base Controller
-----------------------------

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

You will then need to define a [dependency injection](http://php-di.org/) definition, like so:

```php
'controller_dependencies' => DI\add([
    'foo' => DI\get(Foo::class),
]),
```

Use an empty array if none exist or if not using a base controller.
