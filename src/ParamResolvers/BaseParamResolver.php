<?php

namespace JoeSzeto\Capsule\ParamResolvers;

use JoeSzeto\Capsule\Capsule;

class BaseParamResolver
{
    public function __construct(protected Capsule $capsule)
    {
    }
}