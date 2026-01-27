<?php

namespace Essential\Routing\Contracts;

interface RouteCollectionInterface
{
    public function add(RouteInterface $route): void;
    public function all(): array;
    public function getByName(string $name): ?RouteInterface;
    public function match(RequestInterface $request): RouteMatchInterface;
}