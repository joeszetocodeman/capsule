<?php

namespace JoeSzeto\Capsule;

#[\Attribute]
class Setter
{
    protected Capsule $capsule;

    public function __construct(protected string $key)
    {
    }

    public function getKey(): string
    {
        return $this->key;
    }
}