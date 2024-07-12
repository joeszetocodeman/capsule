<?php

namespace JoeSzeto\Capsule;

#[\Attribute]
class OnBlank
{
    protected Capsule $capsule;

    public function __construct(protected string $key)
    {
    }

    public function getCapsule(): Capsule
    {
        return $this->capsule;
    }

    public function setCapsule(Capsule $capsule)
    {
        $this->capsule = $capsule;
        return $this;
    }

    public function isBlank(): bool
    {
        if ( !$this->capsule->has($this->key) ) {
            return true;
        }

        return blank(
            $this->capsule->evaluateKey($this->key)
        );
    }

    public function set($key, $value) : Capsule
    {
        if ( $this->isBlank()) {
            return $this->capsule->set($key, $value);
        }

        return $this->capsule;
    }

}
