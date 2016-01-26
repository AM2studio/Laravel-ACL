# laravel-acl

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

# Install

Via Composer

``` bash
$ composer require am2studio/laravel-acl
```

## Service Provider

in ```config/app.php``` 

under ```'providers'``` add

```php
AM2Studio\LaravelAcl\LaravelAclServiceProvider::class
```

## Config and Migration

``` php
php artisan vendor:publish --provider="AM2Studio\LaravelACL\LaravelACLServiceProvider" --tag=config
php artisan vendor:publish --provider="AM2Studio\LaravelACL\LaravelACLServiceProvider" --tag=migrations
```

## Trait

In user model add following

```php
...
use AM2Studio\LaravelAcl\Traits\LaravelACLTrait;

class User extends Model {
    use LaravelACLTrait, ... ;
}
```

# Usage

## Creating roles

```php
use AM2Studio\LaravelACL\Models\Role;

$userRole = Role::create([
    'name' => 'User',
    'slug' => 'user',
    'description => '',
]);
```

## Attaching and detaching roles

```php
$user = User::find(1);
$user->attachRole($userRole);
$user->detachRole($userRole);
$user->detachAllRoles();
```
## Checking for role

```php
$user->is('user'); // Checkes if user has user role
$user->is('admin|user'); // Checks if user has user OR admin role
$user->is('admin|user', true); // Checks if user has user AND admin role
```

## Creating permissions

```php
use AM2Studio\LaravelACL\Models\Permssion;

$p = Role::create([
    'name' => 'Event edit',
    'slug' => 'event.edit',
    'description => '',
]);
```

## Attaching and detaching permissions


```php
$user = User::find(1);
$user->attachPermission($p);
$user->detachPermission($p);
$user->detachAllPermissions();

$role = Role::find(1);
$role->attachPermission($p);
$role->detachPermission($p);
$role->detachAllPermissions();
```

## Checking for role

```php
$user->can('event.edit'); // Checkes if user has permission
$user->can('event.edit|event.create'); // Checks if user has event.edit OR event.create permission
$user->can('event.edit|event.create', true); // Checks if user has event.edit AND event.create permission
```

## Model permissions

```php

$p2 = Role::create([
    'name' => 'Event edit',
    'slug' => 'event.edit',
    'description => '',
    'model' => 'event',
]);
$p3 = Role::create([
    'name' => 'Event edit',
    'slug' => 'event.edit',
    'description => '',
    'model' => 'event',
    'resource_id' => 1, 
]);

$event = Event::find(1);

$user->attachPermisson($p2);
$user->attachPermisson($p3);

$user->allowed('event.edit', $event); // Checks if user has rights to Event model
$user->allowed('event.edit', $event, $event->id); // Checks if user has rights to Event model with selected id
```

## Blade Extensions

```php
@role('admin')
@endrole

@permission('edit.event')
@endpermission

@allowed('edit.event', $event)
@endallowed

@role('admin|user', 'all')
@endrole
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Credits

- [Marko Šamec][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/am2studio/laravel-acl.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/am2studio/laravel-acl/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/am2studio/laravel-acl.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/am2studio/laravel-acl.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/am2studio/laravel-acl.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/am2studio/laravel-acl
[link-travis]: https://travis-ci.org/am2studio/laravel-acl
[link-scrutinizer]: https://scrutinizer-ci.com/g/am2studio/laravel-acl/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/am2studio/laravel-acl
[link-downloads]: https://packagist.org/packages/am2studio/laravel-acl
[link-author]: https://github.com/msamec
[link-contributors]: ../../contributors
