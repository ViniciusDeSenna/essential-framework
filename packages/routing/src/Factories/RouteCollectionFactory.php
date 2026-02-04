<?php

namespace Essential\Routing\Factories;

use Essential\Routing\Contracts\RouteCollectionFactoryInterface;
use Essential\Routing\Contracts\RouteCollectionInterface;
use Essential\Routing\Contracts\RouterInterface;
use Essential\Routing\RouteCollection;

class RouteCollectionFactory implements RouteCollectionFactoryInterface
{
    public function create(RouterInterface $router): RouteCollectionInterface
    {
        return new RouteCollection($router);
    }
}
