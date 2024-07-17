<?php

use JoeSzeto\Capsule\OnBlank;
use JoeSzeto\Capsule\SetOnBlank;
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
    )->thenReturn(fn(string $name, int $age, string $sex) => [$name, $age, $sex]);
    expect($me)->toBe(['szeto', 30, 'man']);
});

test('set function', function () {
    $me = capsule()
        ->set('name', fn() => 'szeto')
        ->set('age', fn() => 30)
        ->set('sex', fn() => 'man')
        ->thenReturn(fn(string $name, int $age, string $sex) => [$name, $age, $sex]);
    expect($me)->toBe(['szeto', 30, 'man']);
});

test('auto resolve closure params', function () {
    $name = capsule()
        ->set('name', fn() => 'szeto')
        ->through(function (string $name) {
            return $name; // szeto
        })->thenReturn(fn(string $name) => $name);
    expect($name)->toBe('szeto');
});

test('guess name', function () {
    $name = capsule()
        ->set('name', 'szeto')
        ->thenReturn(fn(string $myName) => $myName);
    expect($name)->toBe('szeto');
});

test('on blank', function () {
    $name = capsule()
        ->set('name', fn() => null)
        ->through(
            #[OnBlank('name'), Setter('name')]
            fn() => 'szeto'
        )->thenReturn('name');
    expect($name)->toBe('szeto');
});

test('set on blank', function () {
    $name = capsule()
        ->set('name', fn() => null)
        ->setOnBlank('name', fn() => 'szeto')
        ->thenReturn('name');
    expect($name)->toBe('szeto');
});

test('set on blank attribute', function () {
    $name = capsule()
        ->set('name', fn() => null)
        ->through(
            #[SetOnBlank('name')]
            fn() => 'szeto'
        )
        ->thenReturn('name');
    expect($name)->toBe('szeto');
});