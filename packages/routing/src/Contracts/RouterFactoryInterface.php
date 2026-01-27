<?php

namespace Essential\Routing\Contracts;

interface RouterFactoryInterface
{
    public function create(RouterAdapterInterface $adapter): RouterInterface;
    public function createWithDefaultAdapter(): RouterInterface;
}