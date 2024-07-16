<?php

use Illuminate\Support\Collection;
use JoeSzeto\Capsule\OnBlank;
use function JoeSzeto\Capsule\capsule;

test('key by type', function () {
    capsule()
        ->set(Collection::class, fn() => new Collection())
        ->set(OnBlank::class, fn() => new OnBlank('key'))
        ->set('foo', fn() => new OnBlank('key'))
        ->through(
            fn(OnBlank $foo) => expect($foo)->toBeInstanceOf(OnBlank::class)
        )
        ->thenReturn(
            fn(OnBlank $foo) => expect($foo)->toBeInstanceOf(OnBlank::class)
        );
});

it('will throw when duplicate key type', function () {
    capsule()
        ->set(Collection::class, fn() => new Collection())
        ->set(Collection::class, fn() => new Collection([1]))
        ->thenReturn(
            fn(Collection $collection) => expect($collection)->toBeInstanceOf(Collection::class)
        );
} )->throws(Exception::class);

