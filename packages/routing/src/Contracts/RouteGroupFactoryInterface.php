<?php

namespace Essential\Routing\Contracts;

interface RouteGroupFactoryInterface
{
    /**
     * Create a new RouteGroup instance
     *
     * @param RouterInterface $router
     * @return RouteGroupInterface
     */
    public function create(RouterInterface $router): RouteGroupInterface;
}
