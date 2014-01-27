# Events

The cart fires some events that you can listen for.

Event                    | Parameters        | Description
------------------------ | ----------------- | -----------
cartalyst.cart.added     | $item, $instance  | Fired when an item is added to the cart.
cartalyst.cart.removed   | $item, $instance  | Fired when an item is removed from the cart.
cartalyst.cart.updated   | $item, $instance  | Fired when an item is updated.
cartalyst.cart.cleared   | $instance         | Fired when a cart instance is cleared.
cartalyst.cart.created   | $instance         | Fired when a cart instance is created.
cartalyst.cart.destroyed | $instance         | Fired when a cart instance is destroyed.

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

Whenever a shopping cart instance is cleared.

	Event::listen('cartalyst.cart.cleared', function($instance)
	{
		// Apply your own logic here
	});

Whenever a shopping cart instance is created.

	Event::listen('cartalyst.cart.created', function($instance)
	{
		// Apply your own logic here
	});

Whenever a shopping cart instance is destroyed.

	Event::listen('cartalyst.cart.destroyed', function($instance)
	{
		// Apply your own logic here
	});
