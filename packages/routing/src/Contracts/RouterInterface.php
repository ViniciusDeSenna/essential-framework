<?php

namespace Essential\Routing\Contracts;

interface RouterInterface
{
    /**
     * Handler type is flexible and evaluated by the adapter.
     * @param mixed $handler Handler in any format supported by the adapter (callable, string, array, object, etc.)
     */
    public function get(string $path, mixed $handler): RouteInterface;
    public function post(string $path, mixed $handler): RouteInterface;
    public function put(string $path, mixed $handler): RouteInterface;
    public function patch(string $path, mixed $handler): RouteInterface;
    public function delete(string $path, mixed $handler): RouteInterface;
    public function options(string $path, mixed $handler): RouteInterface;

    /**
     * Add route with flexible handler type.
     * 
     * Handlers are normalized by the adapter, not the core.
     * This allows adapters to support their own handler formats.
     * 
     * @param string|array $methods HTTP method(s)
     * @param string $path Route path
     * @param mixed $handler Handler in any format (callable, string, array, object, etc.)
     */
    public function addRoute(
        string|array $methods, 
        string $path, 
        mixed $handler
    ): RouteInterface;

    public function resource(string $path, string $controller): void;

    public function group(callable $callback): RouteGroupInterface;

    public function match(RequestInterface $request): RouteMatchInterface;
    public function dispatch(RequestInterface $request): ResponseInterface;

    public function url(string $name, array $params = []): string;

    public function getRoutes(): RouteCollectionInterface;
}