<?php

namespace JoeSzeto\Capsule\Features\SupportNamespace;

use JoeSzeto\Capsule\Capsule;

trait SupportNamespace
{
    private string $namespace;

    public function namespace(string $namespace)
    {
        $this->namespace = $namespace;

        NamespaceHolder::instance()->namespaces[$namespace] ??= [];
        NamespaceHolder::instance()->namespaces[$namespace][] = $this;

        return $this;
    }

    public static function getCapsulesInNamespace(string $namespace): array
    {
        return NamespaceHolder::instance()->namespaces[$namespace] ?? [];
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
        NamespaceHolder::instance()->namespaces = [];
    }
}