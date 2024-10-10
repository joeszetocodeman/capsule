<?php

use Illuminate\Contracts\Container\BindingResolutionException;
use function JoeSzeto\Capsule\capsule;

it('can resolve params from namespace', function () {
    capsule()
        ->namespace('some:namespace')
        ->set('name', 'szeto')->run();

    capsule()
        ->namespace('some:namespace')
        ->through(
            fn(string $name) => expect($name)->toBe('szeto')
        )->run();
});

it('can not resolve params from others namespace', function () {
    capsule()
        ->namespace('some:namespace')
        ->set('name', 'szeto')->run();

    capsule()
        ->namespace('other:namespace')
        ->through(
            fn(?string $name) => expect($name)->toBeNull()
        )->run();
});

it('foobar', function () {
    capsule()
        ->namespace('foo:bar')
        ->append(
            fn(?bool $foo) => expect($foo)->toBeNull(),
        );

    capsule()
        ->namespace('foo:bar')
        ->run();
});

