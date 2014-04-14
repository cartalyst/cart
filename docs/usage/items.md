# Items

- [Adding Items](#adding)
- [Updating Items](#updating)
- [Removing Items](#removing)
- [Getting an Item](#getting)

## Adding Items {#adding}

Adding items to the cart is superbly easy.

> **Note:** The required keys are `id`, `name`, `price` and `quantity`.

### Adding a single item into the cart {#single-item}

	Cart::add([
		'id'       => 'foobar1',
		'name'     => 'Foo Bar 1',
		'quantity' => 1,
		'price'    => 12.50,
	]);

### Adding multiple items into the cart {#multiple-items}

	Cart::add([

		[
			'id'       => 'foobar1',
			'name'     => 'Foo Bar 1',
			'quantity' => 1,
			'price'    => 12.50,
		],

		[
			'id'       => 'foobar2',
			'name'     => 'Foo Bar 2',
			'quantity' => 2,
			'price'    => 12.00,
		],

	]);

## Updating items {#updating}

You can update items that are on your cart by updating any property on a cart item.

### Update a single item

	Cart::update('027c91341fd5cf4d2579b49c4b6a90da', [
		'id'       => 'foobar123',
		'name'     => 'Foo Bar 123',
		'quantity' => 1,
		'price'    => 12.50,
	]);

### Update multiple items

	Cart::update([

		'027c91341fd5cf4d2579b49c4b6a90da' => [
			'id'       => 'foobar123',
			'name'     => 'Foo Bar 123',
			'quantity' => 1,
			'price'    => 12.50,
		],

		'56f0ab12a38f8317060d40981f6a4a93' => [
			'id'       => 'bazfoo',
			'name'     => 'Baz Foo',
			'quantity' => 1,
			'price'    => 12.00,
		],

	]);

### Update an item quantity

	Cart::update('027c91341fd5cf4d2579b49c4b6a90da', 2);

## Removing items {#removing}

### Remove a single item

	Cart::remove('027c91341fd5cf4d2579b49c4b6a90da');

### Remove multiple items

Removing multiple items is very easy, you just need to provide an array with the
row id's that you wish to remove.

	Cart::remove([
		'027c91341fd5cf4d2579b49c4b6a90da',
		'56f0ab12a38f8317060d40981f6a4a93',
	]);

## Grab information of an Item

	$item = Cart::item('027c91341fd5cf4d2579b49c4b6a90da');

## Attributes

### Applying attributes to an item

Each item can have different attributes like size, color and you can even add
a price to each attribute that will reflect on the final item price.

	Cart::add([
		'id'         => 'foobar1',
		'name'       => 'Foo Bar 1',
		'quantity'   => 1,
		'price'      => 12.50,
		'attributes' => [

			'size' => [
				'label' => 'L',
				'value' => 'l',
				'price' => 5,
			],

			'color' => [
				'label' => 'Red',
				'value' => 'red',
			],

		],
	]);
