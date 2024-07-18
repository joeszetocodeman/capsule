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
