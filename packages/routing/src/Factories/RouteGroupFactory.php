<?php

namespace Essential\Routing\Factories;

use Essential\Routing\Contracts\RouteGroupFactoryInterface;
use Essential\Routing\Contracts\RouteGroupInterface;
use Essential\Routing\Contracts\RouterInterface;
use Essential\Routing\RouteGroup;

class RouteGroupFactory implements RouteGroupFactoryInterface
{
    public function create(RouterInterface $router): RouteGroupInterface
    {
        return new RouteGroup($router);
    }
}
