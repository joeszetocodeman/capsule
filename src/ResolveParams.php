<?php

namespace JoeSzeto\Capsule;

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
        $parameterName = $param->getName();

        if ( $this->has($parameterName) ) {
            return $this->resolveByName($param);
        }

        $parameterName = $this->resolveByTypeOrClassName($param);

        if ( $this->has($parameterName) ) {
            return value($this->get($parameterName));
        }

        if ( filled($parameterName) ) {
            return app()->make($parameterName);
        }
    }

    protected function resolveByTypeOrClassName(ReflectionParameter $parameter): ?string
    {
        $type = $parameter->getType();

        if ( !$type instanceof ReflectionNamedType ) {
            return null;
        }

        if ( $type->isBuiltin() ) {
            return $this->resolveFromBuiltin($parameter);
        }

        if ( $className = $this->resolveFromClass($parameter) ) {
            return $className;
        }

        return $parameter->getType()->getName();
    }
    /**
     * @param  ReflectionParameter  $param
     * @return array|mixed|null
     */
    public function resolveByName(ReflectionParameter $param): mixed
    {
        $parameterName = $param->getName();
        if ( is_callable($this->get($parameterName)) ) {
            if ( $param->getType()?->getName() === 'Closure' ) {
                return $this->get($parameterName);
            } else {
                return $this->evaluate($this->get($parameterName));
            }
        }
        return $this->get($parameterName);
    }

    /**
     * @return mixed|null
     * @throws \Exception
     */
    public function resolveFromBuiltin(ReflectionParameter $parameter): mixed
    {
        $response = [];

        foreach ($this->getData() as $key => $value) {
            if ( !is_object($value) && gettype($value) === $parameter->getType()->getName() ) {
                $response[] = $key;
            }
        }

        if ( count($response) > 1 ) {
            return $this->guess($parameter, $response);
        }

        return array_pop($response);
    }

    public function resolveFromClass(ReflectionParameter $parameter): mixed
    {
        $response = [];
        foreach ($this->getData() as $key => $value) {
            if ( is_object($value) && get_class($value) === $parameter->getType()->getName() ) {
                $response[] = $key;
            }
        }

        if ( count($response) > 1 ) {
            return $this->guess($parameter, $response);
        }

        return array_pop($response);
    }

    protected function guess(ReflectionParameter $parameter, array $response): mixed
    {
        $highestSimilarity = 0;
        $parameterName = $parameter->getName();
        foreach ($response as $value) {
            similar_text($parameterName, $value, $percent);
            if ( $percent > $highestSimilarity ) {
                $highestSimilarity = $percent;
                $closestMatch = $value;
            }
        }

        if ( !isset($closestMatch) ) {
            throw new \Exception('Multiple values found for type ' . $parameter->getType()->getName() . ' in the capsule.');
        }

        return $closestMatch;
    }
}