<?php

namespace Essential\Routing\Contracts;

interface RouteCollectionInterface
{
    public function prefix(string $prefix): self;
    public function middleware(MiddlewareInterface|string|array $middleware): self;
    public function name(string $name): self;
    public function routes(callable $callback): void;
}