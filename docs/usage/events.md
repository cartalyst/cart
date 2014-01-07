# Events

The cart fires some events that you can listen for.

Event                 | Parameters        | Description
--------------------- | ----------------- | -----------
cart.added            | $item, $instance  | Fired when an item is added to the cart.
cart.removed          | $item, $instance  | Fired when an item is removed from the cart.
cart.updated          | $item, $instance  | Fired when an item is updated.
cart.cleared          | $instance         | Fired when the cart is cleared/destroyed.
cart.instance.created | $instance         | Fired when a cart instance is created.
cart.instance.removed | $instance         | Fired when a cart instance is removed.

## Examples

Whenever an item is added to the shopping cart.

	Event::listen('cart.added', function($item, $instance)
	{
		// Apply your own logic here
	});

Whenever an item is removed from the shopping cart.

	Event::listen('cart.removed', function($item, $instance)
	{
		// Apply your own logic here
	});

Whenever an item is updated on the shopping cart.

	Event::listen('cart.updated', function($item, $instance)
	{
		// Apply your own logic here
	});

Whenever the shopping cart is cleared.

	Event::listen('cart.cleared', function($instance)
	{
		// Apply your own logic here
	});

Whenever a shopping cart instance is created.

	Event::listen('cart.instance.created', function($instance)
	{
		// Apply your own logic here
	});

Whenever a shopping cart instance is removed.

	Event::listen('cart.instance.removed', function($instance)
	{
		// Apply your own logic here
	});
