<?php

namespace Essential;

class Container
{
    protected array $bindings = [];
    protected array $instances = [];

    public function bind(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;

        // Marca como singleton
        if (!isset($this->instances[$abstract])) {
            $this->instances[$abstract] = null;
        }
    }

    public function get(string $abstract): mixed
    {
        // Se já existe instância, retorna
        if (isset($this->instances[$abstract]) && $this->instances[$abstract] !== null) {
            return $this->instances[$abstract];
        }

        // Resolve
        if (!isset($this->bindings[$abstract])) {
            throw new \Exception("No binding for {$abstract}");
        }

        $instance = $this->bindings[$abstract]($this);

        // Se é singleton, armazena
        if (array_key_exists($abstract, $this->instances)) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }
}
