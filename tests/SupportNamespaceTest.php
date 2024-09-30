<?php

use function JoeSzeto\Capsule\capsule;

it('can resolve params from namespace', function () {
    capsule()
        ->namespace('abc')
        ->set('name', 'szeto')->run();


    capsule()
        ->namespace('abc')
        ->through(
            fn(string $name) => expect($name)->toBe('szeto')
        )->run();
});