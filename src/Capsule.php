<?php

namespace JoeSzeto\Capsule;

use ReflectionNamedType;
use ReflectionParameter;

class Capsule
{
    use ResolveParams, WithHalt;

    protected $data = [];

    /** * @var Callback[] */
    protected array $callbacks = [];

    protected array $cachedValues = [];
    private array $throwables = [];

    public function capsule(...$callbacks): static
    {
        return $this->through(...$callbacks);
    }

    public function through(...$callbacks): static
    {
        $callbacks = array_map(fn($callback) => is_callable($callback)
            ? new Callback($callback, $this) : new Callback(fn() => $callback, $this),
            $callbacks
        );
        $this->callbacks = [...$this->callbacks, ...$callbacks];
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function set(string|array $key, $value = null): static
    {
        if ( is_array($key) ) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
            return $this;
        }
        $this->throwIfExistType($key);
        $this->data[$key] = $value;
        $this->cachedValues[$key] = null;
        return $this;
    }


    public function get($key)
    {
        return data_get($this->getData(), $key);
    }

    public function evaluateKey($key)
    {
        if ( !$this->has($key) ) {
            return null;
        }

        if ( is_string($this->get($key)) ) {
            return $this->get($key);
        }

        return $this->cachedValues[$key] ??= $this->evaluate($this->get($key));
    }

    protected function getData()
    {
        $this->data['capsule'] = $this;
        $this->data['set'] = $this->set(...);
        $this->data['halt'] = $this->halt(...);
        return $this->data;
    }

    public function thenReturn(\Closure|string $callback)
    {
        $this->run();

        if ( $this->hasHalt() ) {
            return $this->getHalt();
        }

        if ( is_string($callback) ) {
            return $this->evaluateKey($callback);
        }

        return $this->evaluate($callback);
    }

    public function run()
    {
        try {
            foreach ($this->callbacks as $callback) {
                if ( !$callback->shouldRun() ) {
                    continue;
                }
                $this->evaluate($callback);
            }
        } catch (Halt $e) {
        } catch (\Throwable $e) {
            foreach ($this->callbacks as $callback) {
                $handled = false;
                if ( $callback->isCatch($e) ) {
                    $handled = true;
                    $callback->handle($e);
                }
            }
            if ( !$handled ) {
                throw $e;
            }
        }

        return $this;
    }

    public function evaluate($something, array $params = [])
    {
        if ( $something instanceof Callback ) {
            return $something->evaluate();
        }

        if ( !is_callable($something) ) {
            return $something;
        }

        if ( $params ) {
            $this->set($params);
        }

        return $something(...$this->resolveParams($something));
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

    public function onBlank($key, $value = null)
    {
        $onBlank = (new OnBlank($key))->setCapsule($this);
        if ( is_null($value) ) {
            return $onBlank;
        }

        if ( $onBlank->isBlank() ) {
            $this->through($value);
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

    /**
     * @param  string  $key
     * @return void
     * @throws \Exception
     */
    public function throwIfExistType(string $key): void
    {
        if (! class_exists($key) ) {
            return;
        }
        if ( $this->has($key) ) {
            throw new \Exception("Duplicate key type: $key");
        }
    }


}

