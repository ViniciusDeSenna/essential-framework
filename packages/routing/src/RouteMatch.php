<?php

namespace Essential\Routing;

use Essential\Routing\Contracts\RouteMatchInterface;
use Essential\Routing\Contracts\RouteInterface;

class RouteMatch implements RouteMatchInterface
{
    private bool $matched;
    private ?RouteInterface $route;
    private array $params;

    public function __construct(
        bool $matched,
        ?RouteInterface $route = null,
        array $params = []
    ) {
        $this->matched = $matched;
        $this->route = $route;
        $this->params = $params;
    }

    public function isMatched(): bool
    {
        return $this->matched;
    }

    public function getRoute(): ?RouteInterface
    {
        return $this->route;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $name): mixed
    {
        return $this->params[$name] ?? null;
    }
}