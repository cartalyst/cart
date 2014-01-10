# Introduction

A framework agnostic shopping cart package featuring multiple cart instances and item variants.

The package follows the FIG standard PSR-0 to ensure a high level of
interoperability between shared PHP code and is fully unit-tested.

## Getting started

The package requires at least PHP version 5.3 and comes with a Laravel 4 Facade
and a Service Provider to simplify the optional framework integration.

Have a [read through the Installation Guide]({url}/introduction/installation) and
on how to [Integrate it with Laravel 4]({url}/introduction/laravel-4).

## Quick Example

	// Add a new item to the cart
	Cart::add(array(
		'id'       => 'foobar1',
		'name'     => 'Foo Bar 1',
		'quantity' => 1,
		'price'    => 12.50,
	));

	// Get all the cart items
	$items = Cart::items();

### Method chaining

You can chain some of the methods together, wich means you can do more with only
one single line

> **Note:** Not all the methods are chainable!

	// Get all the items from the 'wishlist' instance
	$items = Cart::instance('wishlist')->items();
