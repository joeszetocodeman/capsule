<?php

namespace JoeSzeto\Capsule;


use Closure;

if (!function_exists('capsule')) {
    function capsule(...$callbacks): Capsule
    {
        return (new Capsule())->capsule(...$callbacks);
    }
}

function append(string $namespace, array $append = []): Capsule
{
    return capsule()
        ->namespace($namespace)
        ->append(...$append);
}

function massage(string $namespace, array $data, string|Closure|null $return = null)
{
    $return = $return ?: str($namespace)->explode(':')->last();
    if (!array_key_exists($return, $data)) {
        throw new \InvalidArgumentException("The key '$return' does not exist in the data array.");
    }
    return capsule()
        ->namespace($namespace)
        ->set($data)
        ->thenReturn($return);
}
