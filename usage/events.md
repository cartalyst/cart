### Events

On this section we have a list of all the events fired by the cart that you can listen for.

Event                   | Parameters        | Description
----------------------- | ----------------- | -----------
cartalyst.cart.added    | $item, $cart      | Event fired when an item is added to the cart.
cartalyst.cart.removed  | $item, $cart      | Event fired when an item is removed from the cart.
cartalyst.cart.update   | $item, $cart      | Event fired when an item is updated.
cartalyst.cart.cleared  | $cart             | Event fired when the cart is cleared.

#### Examples

Whenever an item is added to the shopping cart.

```php
Event::listen('cartalyst.cart.added', function($item, $cart)
{
	// Apply your own logic here
});
```

Whenever an item is removed from the shopping cart.

```php
Event::listen('cartalyst.cart.removed', function($item, $cart)
{
	// Apply your own logic here
});
```

Whenever an item is updated on the shopping cart.

```php
Event::listen('cartalyst.cart.updated', function($item, $cart)
{
	// Apply your own logic here
});
```

Whenever the shopping cart is cleared.

```php
Event::listen('cartalyst.cart.cleared', function($cart)
{
	// Apply your own logic here
});
```
