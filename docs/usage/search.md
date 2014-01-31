# Searching the Cart

## Search for Items

You can use one or multiple properties to search for items in the cart

	Cart::find(array(
		'id'    => 'foobar',
		'name'  => 'Foo Bar',
		'price' => (float) 5,
	));


## Search for Items with attributes

	Cart::find(array(
		'id'   => 'foobar',
		'name' => 'Foo Bar',
		'attributes' => array(
			'size' => array(
				'price' => (float) 5,
			),
		),
	));

>  **Note:** When searching for `price` or `quantity` make sure that the value is a `float`.
