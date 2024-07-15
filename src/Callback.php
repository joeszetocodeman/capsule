<?php

namespace JoeSzeto\Capsule;

use Throwable;

class Callback
{
    protected bool $shouldRun;

    public function __construct(
        public \Closure $callable,
        public Capsule $capsule
    ) {
    }

    protected function findAttributes(string $className)
    {
        $reflection = new \ReflectionFunction($this->callable);
        $attributes = $reflection->getAttributes();

        foreach ($attributes as $attribute) {
            if ( ($instance = $attribute->newInstance()) instanceof $className ) {
                return $instance->setCapsule($this->capsule);
            }
        }

        return null;
    }

    public function shouldRun(): bool
    {
        if ( isset($this->shouldRun) ) {
            return $this->shouldRun;
        }

        return $this->shouldRun = $this->evaluateShouldRun();
    }

    public function isCatch(Throwable $throwable): bool
    {
        if ( $cat = $this->findAttributes(Cat::class) ) {
            return $cat->isCatch($throwable);
        }
        return false;
    }

    public function handle(Throwable $throwable)
    {
        return $this->capsule->evaluate($this->callable, ['message' => $throwable->getMessage()]);
    }

    /**
     * @return bool
     */
    protected function evaluateShouldRun(): bool
    {
        if ( $this->findAttributes(Cat::class) ) {
            return false;
        }

        if ( $onblank = $this->findAttributes(OnBlank::class) ) {
            return $onblank->isBlank();
        }

        return true;
    }
}