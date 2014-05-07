## Exceptions

On this section we provide a list of all the exceptions that are thrown by the cart.

The exceptions are thrown in the `Cartalyst\Cart\Exceptions` namespace.

Exception                         | Description
--------------------------------- | -------------------------------------------
CartMissingRequiredIndexException | This exception will be thrown whenever a required index is not provided.
CartInvalidQuantityException      | This exception will be thrown when the provided quantity is invalid.
CartInvalidPriceException         | This exception will be thrown when the provided price is invalid.
CartInvalidAttributesException    | This exception will be thrown whenever the provided attributes are invalid or malformed.
CartItemNotFoundException         | This exception will be thrown whenever you request an item that does not exist.

### Examples

Catch the exception when adding an item into the cart with a missing required index.

```php
try
{
	# We're not passing the price
	Cart::add([
		'id'       => 'tshirt',
		'name'     => 'T-Shirt',
		'quantity' => 1,
	]);
}
catch (Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException $e)
{
	# Grabbing the missing index
	$missingIndex = $e->getMessage();

	// Apply your own logic here
}
```

Catch the exception when adding an item with an invalid quantity value.

```php
try
{
	Cart::add([
		'id'       => 'tshirt',
		'name'     => 'T-Shirt',
		'quantity' => -1,
		'price'    => 12.50,
	]);
}
catch (Cartalyst\Cart\Exceptions\CartInvalidQuantityException $e)
{
	// Apply your own logic here
}
```

Catch the exception when adding an item with an invalid price value.

```php
try
{
	Cart::add([
		'id'       => 'tshirt',
		'name'     => 'T-Shirt',
		'quantity' => 1,
		'price'    => 'abc',
	]);
}
catch (Cartalyst\Cart\Exceptions\CartInvalidPriceException $e)
{
	// Apply your own logic here
}
```

Catch the exception when adding an item that contains invalid attributes.

```php
try
{
	Cart::add([
		'id'         => 'tshirt',
		'name'       => 'T-Shirt',
		'quantity'   => 1,
		'price'      => 12.50,
		'attributes' => 'abc',
	]);
}
catch (Cartalyst\Cart\Exceptions\CartInvalidAttributesException $e)
{
	// Apply your own logic here
}
```

Catch the exception when trying to update an item that doesn't exist.

```php
try
{
	Cart::update('abc', [
		'price' => 20.00,
	]);
}
catch (Cartalyst\Cart\Exceptions\CartItemNotFoundException $e)
{
	// Apply your own logic here
}
```
