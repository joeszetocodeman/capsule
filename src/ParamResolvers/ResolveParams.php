<?php

namespace JoeSzeto\Capsule\ParamResolvers;

use ReflectionException;
use ReflectionFunction;

trait ResolveParams
{
    protected array $defaultParams = [];

    protected ?array $paramsResolvers = null;

    public function getDefaultParams(): array
    {
        return $this->defaultParams;
    }

    public function defaultParams(array $defaultParams)
    {
        $this->defaultParams = $defaultParams;
        return $this;
    }

    public function paramsResolvers(array $paramsResolvers): static
    {
        $this->paramsResolvers = $paramsResolvers;
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

    public function resolveParam(\ReflectionParameter $param)
    {
        return (new Pipeline)
            ->send($param)
            ->through(
                array_map(function ($resolver) {
                    return new $resolver($this);
                }, $this->getParamsResolvers())
            )->thenReturn();
    }

    public function getParamsResolvers(): array
    {
        return $this->paramsResolvers ??= [
            DefaultParams::class,
            MockName::class,
            MockType::class,
            Name::class,
            Type::class,
            FromNamespace::class,
            Container::class,
        ];
    }


}
