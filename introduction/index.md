## Introduction

A modern and framework agnostic shopping cart package featuring [multiple instances](#instances), [item attributes](#attributes) and [Conditions](https://www.cartalyst.com/manual/conditions).

The package requires PHP 5.4+ and comes bundled with a Laravel 4 and Laravel 5 Facade and a Service Provider to simplify the optional framework integration and follows the FIG standard PSR-4 to ensure a high level of interoperability between shared PHP code and is fully unit-tested.

Have a [read through the Installation Guide](#installation) and on how to [Integrate it with Laravel 4](#laravel-4) or [Integrate it with Laravel 5](#laravel-5).

###### Add a single item to the cart

```php
Cart::add([
	'id'       => 'tshirt',
	'name'     => 'T-Shirt',
	'quantity' => 1,
	'price'    => 12.50,
]);
```

###### Add multiple items to the cart

```php
Cart::add([

	[
		'id'       => 'tshirt',
		'name'     => 'T-Shirt',
		'quantity' => 1,
		'price'    => 12.50,
	],

	[
		'id'       => 'sweatshirt',
		'name'     => 'Sweatshirt',
		'quantity' => 1,
		'price'    => 98.32,
	],

]);
```

###### Get all the cart items

```php
$items = Cart::items();
```
