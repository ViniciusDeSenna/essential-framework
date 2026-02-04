<?php

namespace Essential\Routing;

use Essential\Routing\Contracts\RouteGroupInterface;

class RouteGroup implements RouteGroupInterface
{
    private Router $router;
    private array $attributes = [];

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function prefix(string $prefix): self
    {
        $this->attributes['prefix'] = $prefix;
        return $this;
    }

    public function middleware($middleware): self
    {
        $this->attributes['middleware'] = is_array($middleware) ? $middleware : [$middleware];
        return $this;
    }

    public function name(string $name): self
    {
        $this->attributes['name'] = $name;
        return $this;
    }

    public function routes(callable $callback): void
    {
        $this->router->pushGroup($this->attributes);
        $callback($this->router);
        $this->router->popGroup();
    }
}