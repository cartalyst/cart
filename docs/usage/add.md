# Adding items

## Add a single item to the cart {#single-item}

	Cart::add(array(
		'id'         => 'foobar123',
		'name'       => 'Foo Bar 123',
		'quantity'   => 1,
		'price'      => 12.50,
		'tax'        => array(
			'name'  => 'VAT (17.5%)',
			'value' => 17.5,
		),
		'attributes' => array(
			'size' => array(
				'label' => 'Size',
				'value' => 'L',
				'price' => 5,
			),
		),
	));


## Add multiple items into the cart {#multiple-items}

	Cart::add(array(
		array(
			'id'       => 'foobar123',
			'name'     => 'Foo Bar 123',
			'quantity' => 1,
			'price'    => 12.50,
		),
		array(
			'id'       => 'bazfoo',
			'name'     => 'Baz Foo',
			'quantity' => 1,
			'price'    => 12.00,
			'attributes'  => array(
				'size' => array(
					'label' => 'Size',
					'value' => 'L',
					'price' => 5,
				),
				'color' => array(
					'label' => 'Color',
					'value' => 'Red',
				),
			),
		),
	));


> **Note:** We have optional parameteres like `tax` and `weight` that you can pass
	when adding a product into the cart.
