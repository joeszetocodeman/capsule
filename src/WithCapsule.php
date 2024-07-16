<?php

namespace JoeSzeto\Capsule;

trait WithCapsule
{
    protected Capsule $capsule;

    public function getCapsule(): Capsule
    {
        return $this->capsule;
    }

    public function setCapsule(Capsule $capsule)
    {
        $this->capsule = $capsule;
        return $this;
    }
}