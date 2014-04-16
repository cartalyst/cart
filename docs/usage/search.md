# Searching the Cart

## Search for Items

You can use one or multiple properties to search for items in the cart

	Cart::find([
		'id'    => 'foobar',
		'name'  => 'Foo Bar',
		'price' => 5,
	]);

## Search for Items with attributes

	Cart::find([
		'id'   => 'foobar',
		'name' => 'Foo Bar',
		'attributes' => [

			'size' => [
				'price' => 5,
			],

		],
	]);
