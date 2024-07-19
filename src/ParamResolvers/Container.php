<?php

namespace JoeSzeto\Capsule\ParamResolvers;

class Container extends BaseParamResolver
{
    public function handle(\ReflectionParameter $param, \Closure $next)
    {
        if ( $type = $param->getType()?->getName() ) {
            return app()->make($type);
        }
    }

}