<?php

namespace Essential\Routing\Contracts;

interface RouteCollectionFactoryInterface
{
    /**
     * Create a new RouteCollection instance
     *
     * @param RouterInterface $router
     * @return RouteCollectionInterface
     */
    public function create(RouterInterface $router): RouteCollectionInterface;
}
