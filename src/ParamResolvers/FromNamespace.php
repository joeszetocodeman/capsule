<?php

namespace JoeSzeto\Capsule\ParamResolvers;

use JoeSzeto\Capsule\Capsule;

class FromNamespace extends BaseParamResolver
{
    public function handle(\ReflectionParameter $param, \Closure $next)
    {
        if ( !$this->capsule->underNamespace() ) {
            return $next($param);
        }

        foreach ($this->capsulesInNamespace() as $capsule) {
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

    /**
     * @return Capsule[]
     */
    private function capsulesInNamespace(): array
    {
        /** @var string $namespace */
        $namespace = $this->capsule->getNamespace();
        $capsules = Capsule::getCapsulesInNamespace($namespace);

        return array_filter($capsules, function ($capsule) {
            return $capsule !== $this->capsule;
        });
    }
}