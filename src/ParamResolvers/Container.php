<?php

namespace JoeSzeto\Capsule\ParamResolvers;

use Illuminate\Contracts\Container\BindingResolutionException;

class Container extends BaseParamResolver
{
    public function handle(\ReflectionParameter $param, \Closure $next)
    {
        if ($type = $param->getType()?->getName()) {
            try {
                return app()->make($type);
            } catch (BindingResolutionException $e) {
                return null;
            }
        }
    }

}