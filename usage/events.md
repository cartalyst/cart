### Events

On this section we have a list of all the events fired by the cart that you can listen for.

Event                   | Parameters          | Description
----------------------- | ------------------- | --------------------------------
cartalyst.cart.adding   | $data, $cart        | Event fired before an item is added to the cart.
cartalyst.cart.added    | $item, $cart        | Event fired when an item is added to the cart.
cartalyst.cart.removing | $item, $cart        | Event fired before an item is removed from the cart.
cartalyst.cart.removed  | $item, $cart        | Event fired when an item is removed from the cart.
cartalyst.cart.updating | $item, $data, $cart | Event fired before an item in the cart is updated.
cartalyst.cart.update   | $item, $cart        | Event fired when an item is updated.
cartalyst.cart.created  | $cart               | Event fired when the cart is created.
cartalyst.cart.clearing | $cart               | Event fired before the cart is cleared.
cartalyst.cart.cleared  | $cart               | Event fired when the cart is cleared.

#### Examples

Whenever an item is about to be added to the shopping cart.
```php
Event::listen('cartalyst.cart.adding', function(array $item, $cart)
{
    // Apply your logic here
    // Throw an exception to prevent the item from being added
});
```

Whenever an item is added to the shopping cart.

```php
Event::listen('cartalyst.cart.added', function($item, $cart)
{
	// Apply your own logic here
});
```

Whenever an item is about to be removed to the shopping cart.
```php
Event::listen('cartalyst.cart.removing', function($item, $cart)
{
    // Apply your logic here
    // Throw an exception to prevent the item from being removed
});
```

Whenever an item is removed from the shopping cart.

```php
Event::listen('cartalyst.cart.removed', function($item, $cart)
{
	// Apply your own logic here
});
```

Whenever an item is about to be updated to the shopping cart.
```php
Event::listen('cartalyst.cart.updating', function($item, $newData, $cart)
{
    // Apply your logic here
    // Throw an exception to prevent the item from being updated
});
```

Whenever an item is updated on the shopping cart.

```php
Event::listen('cartalyst.cart.updated', function($item, $cart)
{
	// Apply your own logic here
});
```

Whenever the shopping cart is about to be cleared.
```php
Event::listen('cartalyst.cart.clearing', function($cart)
{
    // Apply your logic here
    // Throw an exception to prevent the cart from being cleared
});
```

Whenever the shopping cart is cleared.

```php
Event::listen('cartalyst.cart.cleared', function($cart)
{
	// Apply your own logic here
});
```
