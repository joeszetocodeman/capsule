<?php

namespace JoeSzeto\Capsule;


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
