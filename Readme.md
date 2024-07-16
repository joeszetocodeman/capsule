
## Basic usage

Setter 
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

set function 
```php
$me = capsule()
    ->set('name', fn() => 'szeto')
    ->set('age', fn() => 30)
    ->set('sex', fn() => 'man')
    ->thenReturn(fn(string $name, int $age, string $sex) => [ $name, $age, $sex ]);
// to be ['szeto', 30, 'man']
```

## Auto resolve params
if the closure has type hints and the type hint is the same as the value of the setter, the value will be resolved automatically 
even if the params name is not 100% match, it will still work
```php
$name = capsule()
    ->set('name', 'szeto')
    ->thenReturn(fn(string $myName) => $myName);
```