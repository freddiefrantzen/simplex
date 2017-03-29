HTTP Middleware
===============

Simplex uses the [relay](http://relayphp.com/) library for invoking [PSR7](http://www.php-fig.org/psr/psr-7/) compatible 
middleware. 


The Middleware Stack
--------------------

Simplex relies on `MatchRoute` and `DispatchController` core middleware. As a minimum requirement, these should be 
included in the middleware stack defined in `/confifg.php`. For convenience, a middleware for setting json response 
headers is also provided. 

Here's an example, bare-bones middleware stack. Note that the order middleware gets added to the stack matters.

```php
<?php

// /config.php

use Simplex\HttpMiddleware\MatchRoute;
use Simplex\HttpMiddleware\DispatchController;
use Simplex\HttpMiddleware\SetJsonResponseHeaders;

return [
    
    'middleware' => [
        MatchRoute::class,
        DispatchController::class,
        SetJsonResponseHeaders::class,
    ],
        
    // ...    
];
```

Feel free to add your own middleware here.
