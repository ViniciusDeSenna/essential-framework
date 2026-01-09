<?php

namespace Essential\Routing\Contracts;

interface ResponseInterface
{
    public function setStatusCode(int $code): self;
    public function getStatusCode(): int;
    public function setHeader(string $name, string $value): self;
    public function setHeaders(array $headers): self;
    public function getHeaders(): array;
    public function setBody(mixed $content): self;
    public function getBody(): mixed;
    public function sent(): void;
}