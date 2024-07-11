<?php


use function JoeSzeto\Capsule\capsule;

test('work', function () {
    capsule(
        fn(Closure $set) => $set('szeto', 'szeto'),
        fn( string $sto, Closure $set ) => $set('abc', $sto. 'abc'  )
    )
        ->thenReturn(
            fn( string $abc ) => expect($abc)->toBe('szetoabc')
        );

    capsule()
        ->set('szeto', fn() => 'szeto')
        ->through(
            fn( string $szeto, Closure $set ) => $set('abc', $szeto. 'abc'  )
        )
        ->thenReturn(
            fn( string $abc ) => expect($abc)->toBe('szetoabc')
        );

    expect(capsule()
        ->set('foo', fn() => 'foo' )
        ->through(
            fn(Closure $foo, Closure $set) => $set('bob', 'bob')
        )->thenReturn('bob'))->toBe('bob');


    capsule()
        ->set('foo', fn() => 'foo' )
        ->thenReturn(
            fn( string $foo ) => expect($foo)->toBe('foo')
        );

    capsule()
        ->set('foo', 'foo' )
        ->set('foo', fn() => 'bar' )
        ->thenReturn(
            fn( string $foo ) => expect($foo)->toBe('bar')
        );

    capsule()
        ->set('foo', fn() => null )
        ->setOnBlank('foo', fn() => 'bar' )
        ->thenReturn(
            fn( Closure $foo ) => expect($foo())->toBe('bar')
        );

    capsule()
        ->set('foo', fn() => 'foo' )
        ->onBlank('foo', fn(Closure $set) => $set('foo', 'bar') )
        ->thenReturn(
            fn( string $foo ) => expect($foo)->toBe('foo')
        );

    capsule()
        ->set('foo')
        ->setOnBlank('foo', fn() => 'abc' )
        ->thenReturn(
            fn( string $foo ) => expect($foo)->toBe('abc')
        );

    capsule()
        ->set('foo', fn() => null )
        ->setOnBlank('foo', fn() => 'abc' )
        ->thenReturn(
            fn( string $foo ) => expect($foo)->toBe('abc')
        );
});
