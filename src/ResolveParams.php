<?php

namespace JoeSzeto\Capsule;

use JoeSzeto\Capsule\ParamResolvers\Container;
use JoeSzeto\Capsule\ParamResolvers\DefaultParams;
use JoeSzeto\Capsule\ParamResolvers\MockName;
use JoeSzeto\Capsule\ParamResolvers\MockType;
use JoeSzeto\Capsule\ParamResolvers\Name;
use JoeSzeto\Capsule\ParamResolvers\Pipeline;
use JoeSzeto\Capsule\ParamResolvers\Type;
use ReflectionException;
use ReflectionFunction;

trait ResolveParams
{
    protected array $defaultParams = [];

    public function getDefaultParams(): array
    {
        return $this->defaultParams;
    }

    public function defaultParams(array $defaultParams)
    {
        $this->defaultParams = $defaultParams;
        return $this;
    }


    /**
     * @throws ReflectionException
     */
    private function resolveParams(\Closure $callback): array
    {
        $reflection = new ReflectionFunction($callback);
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
                    new DefaultParams($this),
                    new MockName($this),
                    new MockType($this),
                    new Name($this),
                    new Type($this),
                    new Container($this)
                ]
            )->thenReturn();
    }
}
