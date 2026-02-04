<?php

namespace Essential\Routing;

use Essential\Routing\Contracts\RouteCollectionInterface;
use Essential\Routing\Contracts\RouteInterface;
use Essential\Routing\Contracts\RequestInterface;
use Essential\Routing\Contracts\RouteMatchInterface;

class RouteCollection implements RouteCollectionInterface
{
    private array $routes = [];
    private array $namedRoutes = [];
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function add(RouteInterface $route): void
    {
        $this->routes[] = $route;
        
        if ($name = $route->getName()) {
            $this->namedRoutes[$name] = $route;
        }
    }

    public function all(): array
    {
        return $this->routes;
    }

    public function getByName(string $name): ?RouteInterface
    {
        return $this->namedRoutes[$name] ?? null;
    }

    public function match(RequestInterface $request): RouteMatchInterface
    {
        // Delega para o Router, que delega para o Adapter
        return $this->router->match($request);
    }
}