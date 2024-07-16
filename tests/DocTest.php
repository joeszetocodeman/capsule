<?php

use JoeSzeto\Capsule\Setter;
use function JoeSzeto\Capsule\capsule;

test('base usage', function () {
    $me = capsule(
        #[Setter('name')]
        fn() => 'szeto',
        #[Setter('age')]
        fn() => 30,
        #[Setter('sex')]
        fn() => 'man'
    )->thenReturn( fn(string $name, int $age, string $sex) => [ $name, $age, $sex ]);
    expect($me)->toBe(['szeto', 30, 'man']);
} );

test('set function', function () {
    $me = capsule()
        ->set('name', fn() => 'szeto')
        ->set('age', fn() => 30)
        ->set('sex', fn() => 'man')
        ->thenReturn(fn(string $name, int $age, string $sex) => [$name, $age, $sex]);
    expect($me)->toBe(['szeto', 30, 'man']);
});