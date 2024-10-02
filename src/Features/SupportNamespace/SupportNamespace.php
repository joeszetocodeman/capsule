<?php

namespace JoeSzeto\Capsule\Features\SupportNamespace;

use JoeSzeto\Capsule\Capsule;

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

    public static function getCapsulesInNamespace(string $namespace): array
    {
        return static::$namespaces[$namespace] ?? [];
    }

    /**
     * @return Capsule[]
     */
    public function getOthersInSameNamespace(): array
    {
        $capsules = static::getCapsulesInNamespace($this->namespace);

        return array_filter($capsules, function ($capsule) {
            return $capsule !== $this;
        });
    }

    public function underNamespace(): bool
    {
        return isset($this->namespace);
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public static function clearNamespace()
    {
        static::$namespaces = [];
    }
}