# Items

- [Adding Items](#adding)
- [Updating Items](#updating)
- [Removing Items](#removing)
- [Getting an Item](#getting)

## Adding Items {#adding}

Adding items to the cart is superbly easy.

> **Note:** The required keys are `id`, `name`, `price` and `quantity`.

### Adding a single item into the cart {#single-item}

	Cart::add(array(
		'id'       => 'foobar1',
		'name'     => 'Foo Bar 1',
		'quantity' => 1,
		'price'    => 12.50,
	));

### Adding multiple items into the cart {#multiple-items}

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

### Applying attributes to an item

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


## Updating items {#updating}

You can update items that are on your cart by updating any property on a cart item.

### Update a single item

	Cart::update('027c91341fd5cf4d2579b49c4b6a90da', array(
		'id'       => 'foobar123',
		'name'     => 'Foo Bar 123',
		'quantity' => 1,
		'price'    => 12.50,
	));

### Update multiple items

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

### Update an item quantity

	Cart::update('027c91341fd5cf4d2579b49c4b6a90da', 2);


## Removing items {#removing}

### Remove a single item

	Cart::remove('027c91341fd5cf4d2579b49c4b6a90da');

### Remove multiple items

Removing multiple items is easy and we provide you with two ways to accomplish this.

#### Method 1

Pass in an array with the row id's you want to remove.

	Cart::remove(array(
		'027c91341fd5cf4d2579b49c4b6a90da',
		'56f0ab12a38f8317060d40981f6a4a93',
	));

#### Method 2

Pass in multiple arguments, where each argument corresponds to an item row id.

	Cart::remove('027c91341fd5cf4d2579b49c4b6a90da', '56f0ab12a38f8317060d40981f6a4a93');


## Grab information of an Item

	$item = Cart::item('027c91341fd5cf4d2579b49c4b6a90da');
