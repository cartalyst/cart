# Events

The cart fires some events that you can listen for.

Event                   | Parameters        | Description
----------------------- | ----------------- | -----------
cartalyst.cart.added    | $item, $instance  | Fired when an item is added to the cart.
cartalyst.cart.removed  | $item, $instance  | Fired when an item is removed from the cart.
cartalyst.cart.update   | $item, $instance  | Fired when an item is updated.
cartalyst.cart.cleared  | $instance         | Fired when the cart is cleared.

## Examples

Whenever an item is added to the shopping cart.

	Event::listen('cartalyst.cart.added', function($item, $instance)
	{
		// Apply your own logic here
	});

Whenever an item is removed from the shopping cart.

	Event::listen('cartalyst.cart.removed', function($item, $instance)
	{
		// Apply your own logic here
	});

Whenever an item is updated on the shopping cart.

	Event::listen('cartalyst.cart.updated', function($item, $instance)
	{
		// Apply your own logic here
	});

Whenever the shopping cart is cleared.

	Event::listen('cartalyst.cart.cleared', function($instance)
	{
		// Apply your own logic here
	});
