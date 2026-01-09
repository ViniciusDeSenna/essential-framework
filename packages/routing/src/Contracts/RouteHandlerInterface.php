<?php

namespace Essential\Routing\Contracts;

interface RouteHandlerInterface
{
    public function handle(RequestInterface $request, array $param = []): ResponseInterface;
}