<?php

namespace Essential\Routing\Adapters;

use Essential\Routing\Contracts\RouteHandlerInterface;
use Essential\Routing\Contracts\RequestContextInterface;
use Essential\Routing\Contracts\HttpRequestInterface;
use Essential\Routing\Contracts\RouteMatchInterface;
use Essential\Routing\Contracts\RouterAdapterInterface;
use Essential\Routing\Contracts\RouteInterface;
use Essential\Routing\RouteMatch;
use Essential\Routing\Route;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

use function FastRoute\simpleDispatcher;

class FastRouteAdapter implements RouterAdapterInterface
{
    private array $routes = [];
    private array $namedRoutes = [];
    private ?Dispatcher $dispatcher = null;

    public function addRoute(
        string|array $methods, 
        string $path, 
        mixed $handler, 
        array $middlewares = []
    ): void {
        $methods = is_array($methods) ? $methods : [$methods];
        
        // Normalize handler according to FastRoute requirements
        $normalizedHandler = $this->normalizeHandlerForFastRoute($handler);
        
        // Cria um objeto Route para armazenar metadados completos
        $route = new Route($methods, $path, $normalizedHandler);
        
        foreach ($middlewares as $middleware) {
            $route->addMiddleware($middleware);
        }
        
        // Usa hash único como identificador
        $routeId = spl_object_hash($route);
        $this->routes[$routeId] = $route;
        
        // Invalida dispatcher
        $this->dispatcher = null;
    }

    public function match(RequestContextInterface $request): RouteMatchInterface
    {
        // FastRoute is HTTP-specific, so we need HTTP request methods
        // RequestInterface (HTTP) extends RequestContextInterface
        if (!$request instanceof HttpRequestInterface) {
            // If request doesn't provide HTTP methods, it's incompatible with FastRoute
            throw new \InvalidArgumentException(
                'FastRouteAdapter requires HttpRequestInterface. ' .
                'Received: ' . get_class($request)
            );
        }
        
        $dispatcher = $this->getDispatcher();
        $method = $request->getMethod();
        $uri = $request->getPath();

        $routeInfo = $dispatcher->dispatch($method, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::FOUND:
                // routeId é o handler que registramos
                $routeId = $routeInfo[1];
                $params = $routeInfo[2];
                
                // Recupera o objeto Route completo
                $route = $this->routes[$routeId] ?? null;
                
                if (!$route) {
                    return new RouteMatch(false);
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

        // Substitui parâmetros
        foreach ($params as $key => $value) {
            // Remove constraint do padrão se existir (ex: {id:\d+} vira {id})
            $path = preg_replace('/\{' . $key . '(:[^}]+)?\}/', $value, $path);
        }

        // Remove parâmetros opcionais não preenchidos
        $path = preg_replace('/\{[^}]+\?\}/', '', $path);

        return $path;
    }

    public function setRouteName(string $name, string $path): void
    {
        $this->namedRoutes[$name] = $path;
    }

    /**
     * Normalize handler according to FastRoute expectations.
     * 
     * This is the adapter's responsibility, not the Router's.
     * Different adapters can normalize handlers in different ways without
     * the Router needing to know about adapter-specific requirements.
     * 
     * @param mixed $handler Handler in any format
     * @return RouteHandlerInterface|callable Normalized handler for FastRoute
     * @throws InvalidArgumentException If handler format is not supported
     */
    private function normalizeHandlerForFastRoute(mixed $handler): RouteHandlerInterface|callable
    {
        // Already in supported format
        if ($handler instanceof RouteHandlerInterface || is_callable($handler)) {
            return $handler;
        }
        
        // String format: "ControllerClass@method" or "ControllerClass::method"
        if (is_string($handler)) {
            $parts = preg_split('/[@::]/', $handler);
            if (count($parts) === 2) {
                return [$parts[0], $parts[1]];
            }
        }
        
        // Array format: ["ControllerClass", "method"]
        if (is_array($handler) && count($handler) === 2) {
            return $handler;
        }
        
        throw new \InvalidArgumentException(
            'Invalid handler format for FastRoute. Expected callable, RouteHandlerInterface, ' .
            'string (Class@method), or array [Class, method].'
        );
    }

    private function getDispatcher(): Dispatcher
    {
        if ($this->dispatcher !== null) {
            return $this->dispatcher;
        }

        $this->dispatcher = simpleDispatcher(function(RouteCollector $r) {
            foreach ($this->routes as $routeId => $route) {
                $methods = $route->getMethods();
                $pattern = $route->getPath();
                
                foreach ($methods as $method) {
                    // Registra routeId como handler para recuperar depois
                    $r->addRoute($method, $pattern, $routeId);
                }
            }
        });

        return $this->dispatcher;
    }
}