<?php

namespace JoeSzeto\Capsule\ParamResolvers;

use JoeSzeto\Capsule\Evaluable;
use ReflectionParameter;

class Name extends BaseParamResolver
{
    public function handle(\ReflectionParameter $param, \Closure $next)
    {
        $parameterName = $param->getName();

        if ( $this->capsule->has($parameterName) ) {
            return $this->resolveByName($param);
        }

        return $next($param);
    }


    /**
     * @param  ReflectionParameter  $param
     * @return array|mixed|null
     */
    public function resolveByName(ReflectionParameter $param): mixed
    {
        $parameterName = $param->getName();

        if ( $this->capsule->hasMock($parameterName) ) {
            return $this->capsule->getMock($parameterName);
        }

        $value = $this->capsule->get($parameterName);
        if ( !is_callable($value) ) {
            return $value;
        }
        if ( $param->getType()?->getName() === Evaluable::class ) {
            return new Evaluable(fn() => $this->capsule->evaluate($this->capsule->get($parameterName)));
        }
        if ( $param->getType()?->getName() === 'Closure' ) {
            return $value;
        }
        return $this->capsule->evaluateKey($parameterName);
    }

}