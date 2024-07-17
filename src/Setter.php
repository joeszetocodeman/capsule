<?php

namespace JoeSzeto\Capsule;

use JoeSzeto\Capsule\contracts\SetterInterface;

#[\Attribute]
class Setter implements SetterInterface
{
    use WithCapsule;

    public function __construct(protected string $key)
    {
    }

    public function getKey(): string
    {
        return $this->key;
    }
}