<?php

namespace Essential\Routing\Contracts;

interface RouteFactoryInterface
{
    /**
     * Create a new Route instance
     *
     * @param string|array $methods HTTP methods
     * @param string $path Route path
     * @param RouteHandlerInterface|callable|array|string $handler Route handler
     * @return RouteInterface
     */
    public function create(
        string|array $methods,
        string $path,
        RouteHandlerInterface|callable|array|string $handler
    ): RouteInterface;
}
