<?php

namespace JoeSzeto\Capsule\ParamResolvers;

use JoeSzeto\Capsule\Capsule;

class MockType extends BaseParamResolver
{
    public function handle(\ReflectionParameter $param, \Closure $next)
    {
        if ( $this->capsule->hasMock($param->getType()?->getName()) ) {
            return $this->capsule->getMock($param->getType()->getName());
        }

        return $next($param);
    }
}