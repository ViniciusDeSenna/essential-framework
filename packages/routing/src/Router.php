<?php

namespace Essential\Routing;

use Essential\Routing\Contracts\RouterInterface;
use Essential\Routing\Contracts\RouterAdapterInterface;
use Essential\Routing\Contracts\RouteInterface;
use Essential\Routing\Contracts\RouteGroupInterface;
use Essential\Routing\Contracts\RouteCollectionInterface;
use Essential\Routing\Contracts\RouteCollectionFactoryInterface;
use Essential\Routing\Contracts\RequestInterface;
use Essential\Routing\Contracts\ResponseInterface;
use Essential\Routing\Contracts\RouteMatchInterface;
use Essential\Routing\Contracts\RouteHandlerInterface;
use Essential\Routing\Contracts\RouteFactoryInterface;
use Essential\Routing\Contracts\RouteGroupFactoryInterface;
use Essential\Routing\Factories\RouteCollectionFactory;
use Essential\Routing\Factories\RouteFactory;
use Essential\Routing\Factories\RouteGroupFactory;

class Router implements RouterInterface
{
    private RouterAdapterInterface $adapter;
    private RouteCollectionInterface $routes;
    private RouteFactoryInterface $routeFactory;
    private RouteCollectionFactoryInterface $routeCollectionFactory;
    private RouteGroupFactoryInterface $routeGroupFactory;
    private array $groupStack = [];

    public function __construct(
        RouterAdapterInterface $adapter,
        ?RouteFactoryInterface $routeFactory = null,
        ?RouteCollectionFactoryInterface $routeCollectionFactory = null,
        ?RouteGroupFactoryInterface $routeGroupFactory = null
    ) {
        $this->adapter = $adapter;
        $this->routeFactory = $routeFactory ?? new RouteFactory();
        $this->routeCollectionFactory = $routeCollectionFactory ?? new RouteCollectionFactory();
        $this->routeGroupFactory = $routeGroupFactory ?? new RouteGroupFactory();
        $this->routes = $this->routeCollectionFactory->create($this);
    }

    public function get(string $path, mixed $handler): RouteInterface
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, mixed $handler): RouteInterface
    {
        return $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, mixed $handler): RouteInterface
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, mixed $handler): RouteInterface
    {
        return $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, mixed $handler): RouteInterface
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    public function options(string $path, mixed $handler): RouteInterface
    {
        return $this->addRoute('OPTIONS', $path, $handler);
    }

    public function addRoute(
        string|array $methods,
        string $path,
        mixed $handler
    ): RouteInterface {
        $path = $this->applyGroupPrefix($path);
        $middlewares = $this->collectGroupMiddlewares();
        
        // Handler is stored as-is; adapter is responsible for normalization
        $route = $this->routeFactory->create($methods, $path, $handler);
        
        foreach ($middlewares as $middleware) {
            $route->addMiddleware($middleware);
        }
        
        $this->routes->add($route);
        
        // Adapter handles handler normalization according to its own format requirements
        $this->adapter->addRoute(
            $methods,
            $path,
            $handler,
            $route->getMiddlewares()
        );
        
        return $route;
    }

    public function resource(string $path, string $controller): void
    {
        $path = rtrim($path, '/');
        $baseName = trim(str_replace('/', '.', $path), '.');
        
        $this->get($path, [$controller, 'index'])
            ->setName("{$baseName}.index");
        
        $this->get("{$path}/{id}", [$controller, 'show'])
            ->where('id', '[0-9]+')
            ->setName("{$baseName}.show");
        
        $this->post($path, [$controller, 'store'])
            ->setName("{$baseName}.store");
        
        $this->put("{$path}/{id}", [$controller, 'update'])
            ->where('id', '[0-9]+')
            ->setName("{$baseName}.update");
        
        $this->delete("{$path}/{id}", [$controller, 'destroy'])
            ->where('id', '[0-9]+')
            ->setName("{$baseName}.destroy");
    }

    public function group(callable $callback): RouteGroupInterface
    {
        $group = $this->routeGroupFactory->create($this);
        $callback($group);
        return $group;
    }

    public function match(RequestInterface $request): RouteMatchInterface
    {
        return $this->adapter->match($request);
    }

    public function dispatch(RequestInterface $request): ResponseInterface
    {
        $match = $this->match($request);
        
        if (!$match->isMatched()) {
            throw new \RuntimeException('Route not found', 404);
        }
        
        $route = $match->getRoute();
        $params = $match->getParams();
        
        return $this->runMiddlewarePipeline($route, $request, $params);
    }

    public function url(string $name, array $params = []): string
    {
        return $this->adapter->generateUrl($name, $params);
    }

    public function getRoutes(): RouteCollectionInterface
    {
        return $this->routes;
    }

    public function pushGroup(array $attributes): void
    {
        $this->groupStack[] = $attributes;
    }

    public function popGroup(): void
    {
        array_pop($this->groupStack);
    }



    private function applyGroupPrefix(string $path): string
    {
        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) {
                $path = '/' . trim($group['prefix'], '/') . '/' . ltrim($path, '/');
            }
        }
        
        return '/' . trim($path, '/');
    }

    private function collectGroupMiddlewares(): array
    {
        $middlewares = [];
        
        foreach ($this->groupStack as $group) {
            if (isset($group['middleware'])) {
                $middlewares = array_merge($middlewares, (array) $group['middleware']);
            }
        }
        
        return $middlewares;
    }

    private function runMiddlewarePipeline(RouteInterface $route, RequestInterface $request, array $params): ResponseInterface
    {
        $middlewares = $route->getMiddlewares();
        $handler = $route->getHandler();
        
        $pipeline = array_reduce(
            array_reverse($middlewares),
            function ($next, $middleware) {
                return function ($request) use ($middleware, $next) {
                    if (is_string($middleware)) {
                        $middleware = new $middleware();
                    }
                    
                    return $middleware->process($request, $next);
                };
            },
            function ($request) use ($handler, $params) {
                if ($handler instanceof RouteHandlerInterface) {
                    return $handler->handle($request, $params);
                }
                
                return call_user_func_array($handler, [$request, $params]);
            }
        );
        
        return $pipeline($request);
    }
}
