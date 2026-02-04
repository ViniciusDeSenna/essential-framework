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

    /**
     * Match a request to a route.
     * 
     * Works with any protocol through RequestContextInterface.
     * HTTP requests provide HttpRequestInterface (extends RequestContextInterface).
     * CLI requests implement RequestContextInterface directly.
     * Event requests implement RequestContextInterface directly.
     * 
     * @param RequestContextInterface $request Protocol-agnostic request context
     * @return RouteMatchInterface The matched route (or not matched)
     */
    public function match(RequestContextInterface $request): RouteMatchInterface;
    
    /**
     * Dispatch a request through the routing and handler pipeline.
     * 
     * Works with any protocol through RequestContextInterface and ResponseContextInterface.
     * 
     * @param RequestContextInterface $request Protocol-agnostic request context
     * @return ResponseContextInterface Protocol-agnostic response context
     */
    public function dispatch(RequestContextInterface $request): ResponseContextInterface;

    public function url(string $name, array $params = []): string;

    public function getRoutes(): RouteCollectionInterface;
}