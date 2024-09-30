<?php

namespace JoeSzeto\Capsule\ParamResolvers;

use JoeSzeto\Capsule\Capsule;

class FromNamespace extends BaseParamResolver
{
    public function handle(\ReflectionParameter $param, \Closure $next)
    {
        if ( !$this->capsule->hasNamespace() ) {
            return $next($param);
        }

        foreach ($this->capsuleInNamespace() as $capsule) {
            /** @var Capsule $capsule */
            $result = $capsule->paramsResolvers([
                DefaultParams::class,
                MockName::class,
                MockType::class,
                Name::class,
                Type::class,
            ])->resolveParam($param);
            if ( $result ) {
                return $result;
            }
        }

        return $next($param);
    }
}