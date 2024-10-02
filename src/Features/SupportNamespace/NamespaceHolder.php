<?php

namespace JoeSzeto\Capsule\Features\SupportNamespace;

class NamespaceHolder
{
    public array $namespaces;

    static function instance(): static
    {
        if ( app()->has('capsule:namespace') ) {
            return app('capsule:namespace');
        }

        $instance = new static();
        app()->singleton('capsule:namespace', fn() => $instance);

        return app('capsule:namespace');
    }

}