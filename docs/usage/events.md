# Events

The cart fires some events that you can listen for.

Event        | Parameters        | Description
------------ | ----------------- | -----------
cart.added   | $item, $instance  | When an item is added to the cart.
cart.removed | $rowId, $instance | When an item is removed from the cart.
cart.updated | $item, $instance  | When an item is updated.
cart.cleared | $instance         | When the cart is cleared/destroyed.

## Example

Whenever an item is added to the shopping cart.

	Event::listen('cart.added', function($item, $instance)
	{
		// Apply your own logic here
	});

Whenever an item is removed from the shopping cart.

	Event::listen('cart.removed', function($rowId, $instance)
	{
		// Apply your own logic here
	});

Whenever an item is updated on the shopping cart.

	Event::listen('cart.updated', function($item, $instance)
	{
		// Apply your own logic here
	});

Whenever shopping cart is cleared.

	Event::listen('cart.cleared', function($instance)
	{
		// Apply your own logic here
	});
