<?php

namespace Essential\Routing\Contracts;

interface RouterInterface
{
    public function get(string $path, RouteHandlerInterface|callable $handler): RouteInterface;
    public function post(string $path, RouteHandlerInterface|callable $handler): RouteInterface;
    public function put(string $path, RouteHandlerInterface|callable $handler): RouteInterface;
    public function patch(string $path, RouteHandlerInterface|callable $handler): RouteInterface;
    public function delete(string $path, RouteHandlerInterface|callable $handler): RouteInterface;
    public function options(string $path, RouteHandlerInterface|callable $handler): RouteInterface;

    public function addRoute(
        string|array $methods, 
        string $path, 
        RouteHandlerInterface|callable $handler
    ): RouterInterface;

    public function resource(string $path, string $controller): void;

    public function group(callable $callback): RouteGroupInterface;

    public function match(RequestInterface $request): RouteMatchInterface;
    public function dispatch(RequestInterface $request): ResponseInterface;

    public function url(string $name, array $params = []): string;

    public function getRoutes(): RouteCollectionInterface;
}