<?php

namespace JoeSzeto\Capsule\ParamResolvers;

class DefaultParams extends BaseParamResolver
{
    public function handle(\ReflectionParameter $param, \Closure $next)
    {
        if ( $this->capsule->getDefaultParams()[$param->getName()] ?? false ) {
            return $this->capsule->getDefaultParams()[$param->getName()];
        }

        return $next($param);
    }
}