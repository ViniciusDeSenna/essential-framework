<?php

namespace Essential\Routing\Adapters;

use Essential\Routing\Contracts\RouteHandlerInterface;
use Essential\Routing\Contracts\RequestInterface;
use Essential\Routing\Contracts\RouteMatchInterface;
use Essential\Routing\Contracts\RouterAdapterInterface;
use Essential\Routing\RouteMatch;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use InvalidArgumentException;

use function FastRoute\simpleDispatcher;

class FastRouteAdapter implements RouterAdapterInterface
{
    private array $routes = [];
    private array $namedRoutes = [];
    private ?Dispatcher $dispatcher = null;

    public function addRoute(string|array $methods, string $path, RouteHandlerInterface|callable $handler, array $middlewares = []): void
    {
        $methods = is_array($methods) ? $methods : [$methods];
        $this->routes[] = [
            'methods' => $methods,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares,
            'name' => null
        ];
        $this->dispatcher = null;
    }

    public function match(RequestInterface $request): RouteMatchInterface
    {
        $dispatcher = $this->getDispatcher();
        $method = $request->getMethod();
        $uri = $request->getPath();

        $routeInfo = $dispatcher->dispatch($method, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $params = $routeInfo[2];

                $middlewares = [];
                foreach ($this->routes as $route) {
                    if ($route['handler'] === $handler) {
                        $middlewares = $route['middlewares'];
                        break;
                    }
                }

                return new RouteMatch(true, $route, $params);

            case Dispatcher::METHOD_NOT_ALLOWED:
            case Dispatcher::NOT_FOUND:
            default:
                return new RouteMatch(false);
        }
    }

    public function generateUrl(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \InvalidArgumentException("Route '{$name}' not found");
        }

        $path = $this->namedRoutes[$name];

        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }

        $path = preg_replace('/\{[^}]+\?\}/', '', $path);

        return $path;
    }

    public function setRouteName(string $name, string $path): void
    {
        $this->namedRoutes[$name] = $path;
    }

    private function getDispatcher(): Dispatcher
    {
        if ($this->dispatcher !== null) {
            return $this->dispatcher;
        }

        $this->dispatcher = simpleDispatcher(function(RouteCollector $r) {
            foreach ($this->routes as $route) {
                foreach ($route['methods'] as $method) {
                    $pattern = $route['path'];
                    $r->addRoute($method, $pattern, $route['handler']);
                }
            }
        });

        return $this->dispatcher;
    }
}