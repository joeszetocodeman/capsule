<?php

namespace JoeSzeto\Capsule;


if ( !function_exists('capsule') ) {
    function capsule(...$callbacks): Capsule
    {
        return (new Capsule())->capsule(...$callbacks);
    }
}
