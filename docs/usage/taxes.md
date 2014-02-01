## Get all the applied taxes

	$taxes = Cart::taxes();
	$taxes = Cart::taxes(false);

## Get the subtotal of the applied taxes

	$taxesSubtotal = Cart::taxesSubtotal();
	$taxesSubtotal = Cart::taxesSubtotal(false);

## Get the total of the applied taxes

	$taxesTotal = Cart::taxesTotal();
	$taxesTotal = Cart::taxesTotal(false);


	Cart::itemsTaxes();

	Cart::itemsTaxesTotal();



### Setting a Tax rate for an item

Another key that you can pass when you add an item to the cart is the 'tax'.

This is an array with a `name` and  `value` or a multidimensional array, where
each array will contain the `name` and the `value`.

The `name` is the Tax identifier.

The `value` is the percentage which you would like to be added onto the price of the item.

In the below example we will use 17.50% for the item tax rate.

	Cart::add(array(
		'id'       => 'foobar1',
		'name'     => 'Foo Bar 1',
		'quantity' => 1,
		'price'    => 12.50,
		'tax'      => array(
			'name'  => 'VAT (17.5%)',
			'value' => 17.5,
		),
	));
