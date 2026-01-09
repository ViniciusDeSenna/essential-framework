<?php

namespace Essential\Routing\Contracts;

interface RouteMatchInterface
{
    public function isMatched(): bool;
    public function getRoute(): ?RouteInterface;
    public function getParams(): array;
    public function getParam(string $name): mixed;
}