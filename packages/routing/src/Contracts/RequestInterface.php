<?php

namespace Essential\Routing\Contracts;

interface RequestInterface
{
    public function getMethod(): string;
    public function getUri(): string;
    public function getPath(): string;
    public function getHeaders(): array;
    public function getHeader(string $name): ?string;
    public function getQueryParams(): array;
    public function getBody(): mixed;
}