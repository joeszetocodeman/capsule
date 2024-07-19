<?php

namespace JoeSzeto\Capsule\ParamResolvers;

use ReflectionNamedType;
use ReflectionParameter;

class Type extends BaseParamResolver
{
    public function handle(\ReflectionParameter $param, \Closure $next)
    {
        $parameterName = $this->resolveByTypeOrClassName($param);

        if ( $this->capsule->has($parameterName) ) {
            return $this->capsule->evaluateKey($parameterName);
        }

        return $next($param);
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

        return $parameter->getName();
    }

    /**
     * @return mixed|null
     * @throws \Exception
     */
    public function resolveFromBuiltin(ReflectionParameter $parameter): mixed
    {
        $response = [];

        foreach ($this->capsule->getData() as $key => $value) {
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
        foreach ($this->capsule->getData() as $key => $value) {
            if ( $key === $parameter->getType()->getName() ) {
                $response[] = $key;
                continue;
            }
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