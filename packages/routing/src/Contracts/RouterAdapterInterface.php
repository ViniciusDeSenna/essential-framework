<?php

namespace Essential\Routing\Contracts;

interface RouterAdapterInterface
{
    public function addRoute(
        string|array $methods, 
        string $path, 
        RouteHandlerInterface|callable $handler, 
        array $middlewares = []
    ): void;
    public function match(RequestInterface $request): RouteMatchInterface;
    public function generateUrl(string $name, array $params = []): string;
}