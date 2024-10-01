<?php

use JoeSzeto\Capsule\Setter;
use function JoeSzeto\Capsule\capsule;

it('support append', function () {
    $capsule = capsule()->through(
        #[Setter('name')]
        fn() => 'szeto'
    );

    $name = $capsule->append(
        #[Setter('name')]
        fn($name) => 'joe ' . $name
    )->thenReturn('name');

    expect($name)->toBe('joe szeto');
});

it('support namespace append', function () {
    capsule()->namespace('abc:foo')->append(
        #[Setter('name')]
        fn(string $name) => 'joe ' . $name
    );

    $name = capsule()
        ->namespace('abc:foo')
        ->through(
            #[Setter('name')]
            fn() => 'szeto'
        )->thenReturn('name');

    expect($name)->toBe('joe szeto');
});