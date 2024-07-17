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

we can also use method ``` setOnBlank ``` to set the OnBlank

```php
$name = capsule()
    ->set('name', fn() => null)
    ->setOnBlank('name', fn() => 'szeto')
    ->thenReturn('name');
```