<?php

namespace JoeSzeto\Capsule;

use ReflectionNamedType;
use ReflectionParameter;
use function Pest\Laravel\options;

class Capsule
{
    use ResolveParams;
    protected $data = [];

    protected array $callbacks = [];

    public function capsule(...$callbacks): static
    {
        $this->callbacks = $callbacks;
        return $this;
    }

    public function through(...$callbacks): static
    {
        $this->callbacks = [...$this->callbacks, ...$callbacks];
        return $this;
    }

    public function set(string|array $key, $value = null): static
    {
        if ( is_array($key) ) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
            return $this;
        }
        $this->data[$key] = $value;
        return $this;
    }


    public function get($key)
    {
        return data_get($this->getData(), $key);
    }

    protected function getData()
    {
        $this->data['capsule'] = $this;
        $this->data['set'] = $this->set(...);
        return $this->data;
    }

    public function thenReturn(\Closure|string $callback)
    {
        $this->run();

        if ( is_string($callback) ) {
            return $this->get($callback);
        }

        return $this->evaluate($callback);
    }

    public function run()
    {
        foreach ($this->callbacks as $callback) {
            $this->evaluate($callback);
        }
        return $this;
    }

    protected function evaluate($something)
    {
        if ( is_callable($something) ) {
            if ( $this->shouldRun($something) ) {
                return $something(...$this->resolveParams($something));
            }
            return null;
        }

        return $something;
    }

    public function call($something)
    {
        return $this->evaluate($something);
    }

    public function has($key): bool
    {
        return array_key_exists($key, $this->getData());
    }

    public function whenEmpty()
    {
        return new WhenEmpty($this);
    }

    public function __call(string $name, $value): self
    {
        return $this->set(
            $name,
            ...$value,
        );
    }

    private function shouldRun(callable $callback)
    {
        $reflection = new \ReflectionFunction($callback);
        $attributes = $reflection->getAttributes();

        foreach ($attributes as $attribute) {
            if ( ($instance = $attribute->newInstance()) instanceof OnBlank ) {
                return $instance->setCapsule($this)->isBlank();
            }
        }
        return true;
    }

    public function onBlank($key, $value = null)
    {
        $onBlank = (new OnBlank($key))->setCapsule($this);
        if (is_null($value)) {
            return $onBlank;
        }

        if ( $onBlank->isBlank() ) {
            $this->callbacks[] = $this->evaluate($value);
        }
        return $this;
    }

    public function onNull()
    {
        return $this->onBlank(...func_get_args());
    }

    public function setOnBlank($key, $value)
    {
        if ( (new OnBlank($key))->setCapsule($this)->isBlank() ) {
            $this->set($key, $value);
        }
        return $this;
    }
}

