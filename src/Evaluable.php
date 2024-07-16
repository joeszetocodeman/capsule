<?php

namespace JoeSzeto\Capsule;

class Evaluable
{
    public function __construct(protected \Closure $closure)
    {
    }

    public function __invoke()
    {
        return app()->call($this->closure);
    }

}