<?php


use Illuminate\Support\Collection;
use JoeSzeto\Capsule\Capsule;
use JoeSzeto\Capsule\Halt;
use JoeSzeto\Capsule\OnBlank;
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

test('proxy', function() {
    capsule()
        ->set('foo', 'foo')
        ->onBlank('foo')
        ->set('foo', fn() => 'bar')
        ->thenReturn(
            fn( string $foo ) => expect($foo)->toBe('foo')
        );

    capsule()
        ->onNull('foo')
        ->set('foo', fn() => 'bar')
        ->thenReturn(
            fn( string $foo ) => expect($foo)->toBe('bar')
        );
} );

test('call', function () {
    expect(
        capsule()->call('foo'))->toBe('foo');
    expect(capsule()->call(fn() => 'foo'))->toBe('foo');

   capsule()
        ->set('foo', 'bar')
        ->call(fn(string $foo) => expect($foo)->toBe('bar'));

    capsule()
        ->set([
            'foo' => '123',
            'bar' => '456'
        ])
        ->call(fn(string $foo, string $b) => expect($foo)
            ->toBe('123')
            ->and($b)->toBe('456')
        );
} );

test('class', function () {
    capsule()
        ->set('something',  new class {
            public $abc = '123';
        } )
    ->call(fn($something) => expect($something->abc)->toBe('123') );

    capsule()
        ->set('something', fn() => new Capsule() )
        ->call(fn( Capsule $something) => expect($something)->toBeInstanceOf(Capsule::class) );
} );

test('attribute', function () {
    capsule()
        ->set('foo', '123')
        ->through(
            #[OnBlank('foo')]
            fn(string $foo, Closure $set) => $set('bar', '456')
        )
        ->thenReturn(
            fn($bar) => expect($bar)->toBeNull()
        );
} );

test('halt', function () {
    $response = capsule()
        ->through(
            fn(Closure $halt) => $halt('yoo'),
            fn() => throw new Exception('hi')
        )
        ->thenReturn(
            fn($bar) => expect($bar)->toBeNull()
        );

    expect($response)->toBe('yoo');
});

test('then return closure', function () {
    expect(capsule()
        ->set('foo', fn() => 'name' )
        ->thenReturn('foo'))->toBe('name');
} );

test('foo', function () {
    expect(capsule()
        ->through(
            fn(Closure $set) => $set('coupons', collect(['1', '2']) ),
            fn(Closure $set, Collection $coupons ) => $set('coupons', null ) )
        )
        ->onBlank('coupons', fn(Closure $set) => $set('coupons', collect(['1', '2', '3']) )
        ->thenReturn('coupons')
        ->toEqual(
            collect(['1', '2', '3'])
            ));
} );
