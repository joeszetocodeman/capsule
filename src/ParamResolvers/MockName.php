<?php

namespace JoeSzeto\Capsule\ParamResolvers;

use JoeSzeto\Capsule\Capsule;

class MockName extends BaseParamResolver
{
    public function handle(\ReflectionParameter $param, \Closure $next)
    {
        $parameterName = $param->getName();

        if ( !$this->capsule->hasMock($parameterName) ) {
            return $next($param);
        }

        if ( $param->getType()->getName() === 'Closure' ) {
            return $this->capsule->getMock($parameterName, true);
        }

        return $this->capsule->getMock($parameterName);

    }
}