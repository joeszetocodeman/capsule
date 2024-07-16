
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