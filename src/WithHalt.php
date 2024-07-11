<?php

namespace JoeSzeto\Capsule;

trait WithHalt
{
    private $halt;

    private function hasHalt() : bool
    {
        return !is_null($this->halt);
    }

    private function getHalt()
    {
        return $this->evaluate($this->halt);
    }

    /**
     * @throws Halt
     */
    public function halt($value = null) {
        $this->halt = $value;
        throw new Halt($value);
    }
}