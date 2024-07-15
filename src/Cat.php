<?php

namespace JoeSzeto\Capsule;

use Throwable;

#[\Attribute]
class Cat
{
    private Capsule $capsule;

    public function __construct(protected string $exceptionClass)
    {
    }

    public function isCatch(Throwable $throwable)
    {
        return get_class($throwable) === $this->exceptionClass;
    }

    public function setCapsule(Capsule $capsule)
    {
        $this->capsule = $capsule;
        return $this;
    }
}