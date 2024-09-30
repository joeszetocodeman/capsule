<?php

namespace JoeSzeto\Capsule\Features\SupportNamespace;

trait SupportNamespace
{
    private static array $namespaces;
    private string $namespace;

    public function namespace(string $namespace)
    {
        $this->namespace = $namespace;

        static::$namespaces[$namespace] ??= [];
        static::$namespaces[$namespace][] = $this;

        return $this;
    }

    public function hasNamespace(): bool
    {
        return isset($this->namespace);
    }

}