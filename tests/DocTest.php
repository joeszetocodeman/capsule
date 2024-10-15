<?php

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Model;
use JoeSzeto\Capsule\Capsule;
use JoeSzeto\Capsule\Cat;
use JoeSzeto\Capsule\Each;
use JoeSzeto\Capsule\Evaluable;
use JoeSzeto\Capsule\OnBlank;
use JoeSzeto\Capsule\Only;
use JoeSzeto\Capsule\SetOnBlank;
use JoeSzeto\Capsule\Setter;
use JoeSzeto\Capsule\Skip;
use JoeSzeto\Capsule\WhenEmpty;
use function JoeSzeto\Capsule\capsule;
use function JoeSzeto\Capsule\massage;

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

test('Closure', function () {
    $name = capsule()
        ->set('name', fn() => 'szeto')
        ->thenReturn(fn(Closure $name) => $name());
    expect($name)->toBe('szeto');
});

test('Evaluable', function () {
    capsule()
        ->set('name', fn() => 'szeto')
        ->set('something', function (string $name) {
            expect($name)->toBe('szeto');
        })
        ->thenReturn(
            fn(Evaluable $something) => $something()
        );
});

describe('mock', function () {
    test('mock string', function () {
        Capsule::mock('name', 'szeto');
        capsule()
            ->set('name', fn() => 'someone')
            ->through(
                fn(string $name) => expect($name)->toBe('szeto')
            )->run();
    });

    test('mock function', function () {
        Capsule::mock('name', fn() => 'szeto');
        capsule()
            ->set('name', fn() => 'someone')
            ->through(
                fn(Closure $name) => expect($name())->toBe('szeto')
            )->run();
    });

    test('mock class', function () {
        Capsule::mock('name', new OnBlank('szeto'));
        capsule()
            ->set('name', new OnBlank('someone'))
            ->through(
                fn(OnBlank $name) => expect($name->getKey())->toBe('szeto')
            )->run();
    });

    test('mock with sequence', function () {
        Capsule::reset();
        Capsule::mock(OnBlank::class, new Sequence(
            new OnBlank('szeto'), new OnBlank('joe')
        ));
        capsule()
            ->through(
                fn(OnBlank $name) => expect($name->getKey())->toBe('szeto'),
                fn(OnBlank $name) => expect($name->getKey())->toBe('joe')
            )->run();
    });
});

test('solve by app container', function () {
    Capsule::reset();
    capsule()
        ->thenReturn(fn(WhenEmpty $empty) => expect($empty)->toBeInstanceOf(WhenEmpty::class));
});

test('only', function () {
    capsule()
        ->through(
            fn() => throw new Exception('should not run'),
            #[Only]
            fn() => expect(true)->toBeTrue()
        )
        ->run();
});

test('skip', function () {
    capsule()
        ->through(
            fn() => expect(true)->toBeTrue(),
            #[Skip]
            fn() => throw new Exception('should not run'),
        )
        ->run();
});

test('forEach', function () {
    capsule()
        ->through(
            #[Setter('items')]
            fn() => collect(['foo', 'bar']),
            #[Each('items', as: 'item')]
            fn(string $item) => expect($item)->toBeString()
        )
        ->run();
});

test('use invoke class', function () {
    capsule()
        ->through(
            #[Setter('name')]
            fn() => 'joe',
            new class {
                #[Setter('name')]
                public function __invoke(string $name)
                {
                    return $name.' szeto';
                }
            },
            fn(string $name) => expect($name)->toBe('joe szeto')
        )
        ->run();
});

test('catch', function () {
    capsule(
        fn() => throw new Exception('foo'),
        #[Cat(Exception::class)]
        fn($message) => expect($message)->toBe('foo')
    )->run();
});

describe('massage', function () {
    it('return last past of namespace', function () {
        $bob = massage('foo:bar:bob', [
            'bob' => 'szeto'
        ]);
        expect($bob)->toBe('szeto');
    });

    it('return return', function () {
        $bob = massage('foo:bar:abc', [
            'bob' => 'szeto'
        ], 'bob');
        expect($bob)->toBe('szeto');
    });

    it('throw when not match', function () {
        $bob = massage('foo:bar:abc', [
            'bob' => 'szeto'
        ]);
        expect($bob)->toBe('szeto');
    })->throws(InvalidArgumentException::class);
});

