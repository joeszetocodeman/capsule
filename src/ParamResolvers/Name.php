<?php

namespace JoeSzeto\Capsule\ParamResolvers;

class Name extends BaseParamResolver
{
    public function handle(\ReflectionParameter $param, \Closure $next)
    {
        $parameterName = $param->getName();

        if ( $this->capsule->has($parameterName) ) {
            return $this->capsule->resolveByName($param);
        }

        return $next($param);
    }

}