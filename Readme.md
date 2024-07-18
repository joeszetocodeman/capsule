## Install

```
composer require joe.szeto/capsule
```

## Basic usage

we can use attribute ``` Setter ``` to tell the code
the following Closure is a setter, and the value of the setter is the return value of the closure

```php
$me = capsule(
    #[Setter('name')]
    fn() => 'szeto',
    #[Setter('age')]
    fn() => 30,
    #[Setter('sex')]
    fn() => 'man'
)->thenReturn(fn(string $name, int $age, string $sex) => [ $name, $age, $sex ]);
// to be ['szeto', 30, 'man']
```

we can also use method ``` set ``` to set the value of the setter

```php
$me = capsule()
    ->set('name', fn() => 'szeto')
    ->set('age', fn() => 30)
    ->set('sex', fn() => 'man')
    ->thenReturn(fn(string $name, int $age, string $sex) => [ $name, $age, $sex ]);
// to be ['szeto', 30, 'man']
```

## Auto resolve params

if the closure has type hints and the type hint is the same as the value of the setter, the value will be resolved
automatically
even if the params name is not 100% match, it will still work

```php
$name = capsule()
    ->set('name', 'szeto')
    ->thenReturn(fn(string $myName) => $myName);
```

## OnBlank

it will only call when the value of the setter is null

```php
$name = capsule()
    ->set('name', fn() => null)
    ->through(
        #[OnBlank('name'), Setter('name')]
        fn() => 'szeto' // this closure only call when the value of 'name' is null
    )->thenReturn('name');
```

we can also use attribute ``` SetOnBlank ``` to set and detect the OnBlank at the same time

```php
 $name = capsule()
        ->set('name', fn() => null)
        ->through(
            #[SetOnBlank('name')]
            fn() => 'szeto'
        )
        ->thenReturn('name');
```

we can also use method ``` setOnBlank ``` to set the OnBlank

```php
$name = capsule()
    ->set('name', fn() => null)
    ->setOnBlank('name', fn() => 'szeto')
    ->thenReturn('name');
```

### Closure

if the set value is a closure,
when the type hint of the param of using is Closure
the original closure will be passed to the param

```php
    $name = capsule()
        ->set('name', fn() => 'szeto')
        ->thenReturn(fn(Closure $name) => $name());
    expect($name)->toBe('szeto');
```

when the type hint of the params is a NOT closure,
the value will be return

```php
    $name = capsule()
        ->set('name', fn() => 'szeto')
        ->thenReturn(
            fn(string $name) => $name // now name is szeto, not a closure
        );
    expect($name)->toBe('szeto');
```

### Evaluable

if the type is closure, and call it we have to pass the params manually

```php
    $name = capsule()
        ->set('prefix', fn() => 'Joe')
        ->set('name', fn(string $prefix) => $prefix. ' szeto')
        ->thenReturn(
            fn(Closure $name, string $prefix) => $name($prefix) // params is Joe
        );
```

but if we pass the param one by one manually it will be very tedious
now Evaluable become handy

```php
capsule()
    ->set('prefix', fn() => 'Joe')
    ->set('name', fn(string $prefix) => $prefix. ' szeto')
    ->thenReturn(
        fn(Evaluable $name) => $name() // params is Joe
    );
```

### Mocking

The Capsule::mock method allows developers to replace parts of their application's behavior with predetermined responses
or operations.
This is particularly useful in testing, where you want to isolate the part of the application you are testing and
control its interactions with external dependencies.

```php
public static function mock(string $key, mixed $value): void
```

To replace a string value within the capsule, simply pass the key and the new string value to the mock method.

```php
Capsule::mock('name', 'szeto');
```

If you need to mock the behavior of a function, provide a closure as the second argument. This closure will be executed
in place of the original function associated with the given key.

```php
Capsule::mock('name', fn() => 'szeto');
```

To mock an object, pass an instance of the class as the second argument. This instance will replace any existing
instances bound to the specified key within the capsule.

```php
Capsule::mock('name', new OnBlank('szeto'));
```