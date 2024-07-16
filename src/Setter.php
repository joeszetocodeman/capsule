<?php

namespace JoeSzeto\Capsule;

#[\Attribute]
class Setter
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