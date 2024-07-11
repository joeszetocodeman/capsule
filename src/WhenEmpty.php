<?php

namespace JoeSzeto\Capsule;

class WhenEmpty
{
    public function __construct(protected Capsule $capsule)
    {
    }

    public function __call(string $name, $value)
    {
        if ( blank($this->capsule->get($name)) ) {
            return $this->capsule->set($name, ...$value);
        }

        return $this->capsule;
    }
}
