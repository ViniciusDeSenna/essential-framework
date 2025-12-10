<?php

namespace Essential;

class Application
{
    protected Container $container;
    protected string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        $this->container = new Container();

        // Registra a aplicação no container
        $this->container->singleton(Application::class, fn() => $this);
    }

    public function register(string $abstract, callable $concrete): void
    {
        $this->container->bind($abstract, $concrete);
    }

    public function singleton(string $abstract, callable $concrete): void
    {
        $this->container->singleton($abstract, $concrete);
    }

    public function get(string $abstract): mixed
    {
        return $this->container->get($abstract);
    }

    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path ? '/' . $path : '');
    }
}
