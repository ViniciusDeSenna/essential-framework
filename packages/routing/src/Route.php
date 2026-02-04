<?php

namespace Essential\Routing;

use Essential\Routing\Contracts\RouteHandlerInterface;
use Essential\Routing\Contracts\RouteInterface;

class Route implements RouteInterface
{
    private string|array $methods;
    private string $path;
    private RouteHandlerInterface|callable $handler;
    private array $middlewares = [];
    private ?string $name = null;
    private array $constraints = [];

    public function __construct(string|array $methods, string $path, $handler)
    {
        $this->methods = is_array($methods) ? $methods : [$methods];
        $this->path = $path;
        $this->handler = $handler;
    }

    public function getMethod(): string
    {
        return implode('|', $this->methods);
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHandler(): RouteHandlerInterface|callable
    {
        return $this->handler;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function addMiddleware($middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function where(string $param, string $pattern): self
    {
        $this->constraints[$param] = $pattern;
        $this->path = preg_replace(
            '/\{' . $param . '\}/',
            '{' . $param . ':' . $pattern . '}',
            $this->path
        );
        
        return $this;
    }
}