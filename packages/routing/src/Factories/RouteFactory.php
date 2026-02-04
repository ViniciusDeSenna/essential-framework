<?php

namespace Essential\Routing\Factories;

use Essential\Routing\Contracts\RouteFactoryInterface;
use Essential\Routing\Contracts\RouteHandlerInterface;
use Essential\Routing\Contracts\RouteInterface;
use Essential\Routing\Route;

class RouteFactory implements RouteFactoryInterface
{
    public function create(
        string|array $methods,
        string $path,
        RouteHandlerInterface|callable|array|string $handler
    ): RouteInterface {
        return new Route($methods, $path, $handler);
    }
}
