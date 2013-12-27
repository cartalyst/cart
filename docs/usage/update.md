# Updating items

## Update a single item {#single-item}

	Cart::update('027c91341fd5cf4d2579b49c4b6a90da', array(
		'id'       => 'foobar123',
		'name'     => 'Foo Bar 123',
		'quantity' => 1,
		'price'    => 12.50,
	));

## Update multiple items {#multiple-items}

	Cart::update(array(
		'027c91341fd5cf4d2579b49c4b6a90da' => array(
			'id'       => 'foobar123',
			'name'     => 'Foo Bar 123',
			'quantity' => 1,
			'price'    => 12.50,
		),
		'56f0ab12a38f8317060d40981f6a4a93' => array(
			'id'       => 'bazfoo',
			'name'     => 'Baz Foo',
			'quantity' => 1,
			'price'    => 12.00,
		),
	));


## Update an item quantity {#item-quantity}

	Cart::update('027c91341fd5cf4d2579b49c4b6a90da', 2);
