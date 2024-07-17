<?php

namespace JoeSzeto\Capsule;

use JoeSzeto\Capsule\contracts\OnBlankInterface;

#[\Attribute]
class OnBlank implements OnBlankInterface
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

    public function set($key, $value): Capsule
    {
        if ( $this->isBlank() ) {
            return $this->capsule->set($key, $value);
        }

        return $this->capsule;
    }

}
