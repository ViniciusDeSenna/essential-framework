<?php

namespace Essential\Routing;

use Essential\Routing\Contracts\RouteInterface;
use Essential\Routing\Contracts\RouteMatchInterface;

class RouteMatch implements RouteMatchInterface
{
    public function __construct(
        private bool $matched,
        private ?RouteInterface $route = null,
        private array $params = []
    ) {}

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