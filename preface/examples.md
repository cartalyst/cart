### Examples

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
