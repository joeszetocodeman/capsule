<?php

namespace JoeSzeto\Capsule;

#[\Attribute]
class Each
{
    use WithCapsule;

    public function __construct(public string $key)
    {
    }
}