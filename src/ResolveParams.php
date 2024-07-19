<?php

namespace JoeSzeto\Capsule;

use JoeSzeto\Capsule\ParamResolvers\Container;
use JoeSzeto\Capsule\ParamResolvers\MockName;
use JoeSzeto\Capsule\ParamResolvers\MockType;
use JoeSzeto\Capsule\ParamResolvers\Name;
use JoeSzeto\Capsule\ParamResolvers\Pipeline;
use JoeSzeto\Capsule\ParamResolvers\Type;
use ReflectionNamedType;
use ReflectionParameter;

trait ResolveParams
{
    private function resolveParams(\Closure $callback): array
    {
        $reflection = new \ReflectionFunction($callback);
        $params = $reflection->getParameters();
        $resolved = [];
        foreach ($params as $param) {
            $resolved[] = $this->resolveParam($param);
        }
        return $resolved;
    }

    private function resolveParam(\ReflectionParameter $param)
    {
        return (new Pipeline)->send($param)
            ->through(
                [
                    new MockName($this),
                    new MockType($this),
                    new Name($this),
                    new Type($this),
                    new Container($this)
                ]
            )->thenReturn();
    }


    /**
     * @param  ReflectionParameter  $param
     * @return array|mixed|null
     */
    public function resolveByName(ReflectionParameter $param): mixed
    {
        $parameterName = $param->getName();

        if ( $this->hasMock($parameterName) ) {
            return $this->getMock($parameterName);
        }


        $value = $this->get($parameterName);
        if ( !is_callable($value) ) {
            return $value;
        }
        if ( $param->getType()?->getName() === Evaluable::class ) {
            return new Evaluable(fn() => $this->evaluate($this->get($parameterName)));
        }
        if ( $param->getType()?->getName() === 'Closure' ) {
            return $value;
        }
        return $this->evaluateKey($parameterName);
    }


}
