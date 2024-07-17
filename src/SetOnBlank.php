<?php

namespace JoeSzeto\Capsule;

use JoeSzeto\Capsule\contracts\OnBlankInterface;
use JoeSzeto\Capsule\contracts\SetterInterface;

#[\Attribute]
class SetOnBlank implements SetterInterface, OnBlankInterface
{
    use WithCapsule;

    public function __construct(protected string $key)
    {
    }

    protected OnBlank $onBlank;
    protected Setter $setter;

    public function getOnBlank(): OnBlank
    {
        return $this->onBlank;
    }

    public function onBlank(OnBlank $onBlank): SetOnBlank
    {
        $this->onBlank = $onBlank;
        return $this;
    }

    public function getSetter(): Setter
    {
        return $this->setter;
    }

    public function setter(Setter $setter): SetOnBlank
    {
        $this->setter = $setter;
        return $this;
    }


    public function isBlank(): bool
    {
        return (new OnBlank($this->getKey()))->setCapsule($this->capsule)->isBlank();
    }

    public function getKey(): string
    {
        return $this->key;
    }
}