<?php

namespace Essential\Routing\Contracts;

interface MiddlewareInterface
{
    public function process(RequestInterface $request, callable $next): ResponseInterface;
}