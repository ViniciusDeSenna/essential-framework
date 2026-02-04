<?php

namespace Essential\Routing\Contracts;

interface RouterAdapterInterface
{
    /**
     * Register route with adapter-specific handler normalization.
     * 
     * The adapter is responsible for interpreting and normalizing the handler
     * according to its own requirements. This allows different adapters to support
     * different handler formats without Router imposing a single standard.
     * 
     * @param string|array $methods HTTP method(s)
     * @param string $path Route path
     * @param mixed $handler Handler in any format - adapter decides interpretation
     * @param array $middlewares Middleware stack for this route
     */
    public function addRoute(
        string|array $methods, 
        string $path, 
        mixed $handler, 
        array $middlewares = []
    ): void;
    public function match(RequestInterface $request): RouteMatchInterface;
    public function generateUrl(string $name, array $params = []): string;
}