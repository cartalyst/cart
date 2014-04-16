# Introduction

A framework agnostic shopping cart package featuring multiple cart instances, item attributes and [Conditions](http://www.cartalyst.com/manual/conditions).

The package follows the FIG standard PSR-4 to ensure a high level of interoperability between shared PHP code and is fully unit-tested.

## Getting started

The package requires PHP 5.4+ and comes bundled with a Laravel 4 Facade and a Service Provider to simplify the optional framework integration.

Have a [read through the Installation Guide]({url}/introduction/installation) and
on how to [Integrate it with Laravel 4]({url}/introduction/laravel-4).

## Quick Example

### Add a single item to the cart

	Cart::add([
		'id'       => 'foobar1',
		'name'     => 'Foo Bar 1',
		'quantity' => 1,
		'price'    => 12.50,
	]);

### Add multiple items to the cart

	Cart::add([

		[
			'id'       => 'foobar1',
			'name'     => 'Foo Bar 1',
			'quantity' => 1,
			'price'    => 12.50,
		],

		[
			'id'       => 'foobar2',
			'name'     => 'Foo Bar 2',
			'quantity' => 2,
			'price'    => 12.00,
		],

	]);

### Get all the cart items

	$items = Cart::items();
