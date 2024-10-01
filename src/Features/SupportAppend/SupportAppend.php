<?php

namespace JoeSzeto\Capsule\Features\SupportAppend;


use JoeSzeto\Capsule\Callback;

trait SupportAppend
{

    protected array $appends = [];

    public function getAppends(): array
    {
        return $this->appends;
    }

    public function append(...$callbacks): static
    {
        $this->appends = [...$this->appends, ...$callbacks];
        return $this;
    }
}