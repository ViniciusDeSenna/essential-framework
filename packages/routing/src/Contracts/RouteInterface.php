<?php

namespace Essential\Routing\Contracts;

interface RouteInterface
{
    public function getMethod(): string;
    public function getPath(): string;
    public function getHandler(): RouteHandlerInterface|callable;
    public function getMiddlewares(): array;
    public function getName(): ?string;
    public function setName(string $name): self;
    public function addMiddleware(MiddlewareInterface|string $middleware): self;
    public function getConstraints(): array;
    public function where(string $param, string $pattern): self;
}