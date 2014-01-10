# Adding items

You can add items to the cart and it is superbly easy.

> **Note:** The required keys are `id`, `name`, `price` and `quantity`.

## Add a single item to the cart {#single-item}

	Cart::add(array(
		'id'       => 'foobar1',
		'name'     => 'Foo Bar 1',
		'quantity' => 1,
		'price'    => 12.50,
	));

## Add multiple items into the cart {#multiple-items}

	Cart::add(array(
		array(
			'id'       => 'foobar1',
			'name'     => 'Foo Bar 1',
			'quantity' => 1,
			'price'    => 12.50,
		),
		array(
			'id'       => 'foobar2',
			'name'     => 'Foo Bar 2',
			'quantity' => 1,
			'price'    => 12.00,
		),
	));

## Setting a Tax rate for an item

Another key that you can pass when you add an item to the cart is the 'tax'.

This is an array with a `name` and  `value` or a multidimensional, where each array
will contain the `name` and the `value`.

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

## Applying attributes to an item

	Cart::add(array(
		'id'         => 'foobar1',
		'name'       => 'Foo Bar 1',
		'quantity'   => 1,
		'price'      => 12.50,
		'attributes' => array(
			'size' => array(
				'label' => 'L',
				'value' => 'l',
				'price' => 5,
			),
			'color' => array(
				'label' => 'Red',
				'value' => 'red',
			),
		),
	));
