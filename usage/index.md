## Usage

In this section we'll show how you can manage your shopping cart.

#### Add Item

Having the ability to add items to the shopping cart is crucial and we've made it incredible simple to do it.

You can pass a simple or a multidimensional array and to help you get started, we have listed below all the default `indexes` that you can pass when adding or updating a cart item.

<a id="indexes"></a>

Key        | Required | Type   | Description
---------- | -------- | ------ | ----------------------------------------------
id         | true     | mixed  | The item unique identifier, can be a numeric id, an sku, etc..
name       | true     | string | The item name.
price      | true     | float  | The item price.
quantity   | true     | int    | The quantity, needs to be an integer and can't be a negative value.
attributes | false    | array  | The item [attributes](#attributes) like size, color, fabric, etc..
weight     | false    | float  | The item weight.

> **Note:** You can pass custom `key`/`value` pairs into the array when adding or updating an item, please check the examples below.

#### Cart::add()

Param   | Required | Type   | Description
------- | -------- | ------ | -------------------------------------------------
$item   | true     | array  | A single or multidimensional array that respects the list of [indexes](#indexes) above.

#### Add a single item

```php
Cart::add([
	'id'       => 'tshirt',
	'name'     => 'T-Shirt',
	'quantity' => 1,
	'price'    => 12.50,
]);
```

#### Add a single item with a custom `index`

```php
Cart::add([
	'id'       => 'tshirt',
	'name'     => 'T-Shirt',
	'quantity' => 1,
	'price'    => 12.50,
	'sku'      => 'tshirt-custom',
]);
```

#### Add a single item with `attributes` and a custom `index`

```php
Cart::add([
	'id'         => 'tshirt',
	'name'       => 'T-Shirt',
	'quantity'   => 1,
	'price'      => 12.50,
	'sku'        => 'tshirt-red-large',
	'attributes' => [

		'color' => [
			'label' => 'Red',
			'value' => 'red',
		],

		'size' => [
			'label' => 'Large',
			'value' => 'l',
		],

	],
]);
```

#### Adding multiple items

```php
Cart::add([

	[
		'id'         => 'tshirt',
		'name'       => 'T-Shirt',
		'quantity'   => 1,
		'price'      => 12.50,
		'sku'        => 'tshirt-red-large',
		'attributes' => [

			'color' => [
				'label' => 'Red',
				'value' => 'red',
			],

			'size' => [
				'label' => 'Large',
				'value' => 'l',
			],

		],
	],

	[
		'id'       => 'sweatshirt',
		'name'     => 'Sweatshirt',
		'quantity' => 1,
		'price'    => 98.32,
	],

]);
```

### Update Item

Updating items is as simple as adding them.

#### Cart::update()

Param   | Required | Type   | Description
------- | -------- | ------ | -------------------------------------------------
$rowId  | true     | string | The item row id.
$data   | true     | mixed  | This can be either an array or an integer, if an integer, it'll update the item quantity.

> **Note:** If the `$data` is an array, it doesn't require you to pass all the `indexes`, just the ones you wish to update, like `name`, `price`, `quantity`, `attributes`, etc..

#### Update an item quantity

```php
Cart::update('c14c437bc9ae7d35a7c18ee151c6acc0', 2);
```

#### Update a single item

```php
Cart::update('c14c437bc9ae7d35a7c18ee151c6acc0', [
	'quantity' => 1,
	'price'    => 12.50,
]);
```

#### Update multiple items

```php
Cart::update([

	'c14c437bc9ae7d35a7c18ee151c6acc0' => [
		'id'       => 'tshirt',
		'name'     => 'T-Shirt',
		'quantity' => 1,
		'price'    => 12.50,
	],

	'63e2d7033fe95b9134a5737503d10ba5' => [
		'id'       => 'sweatshirt',
		'name'     => 'Sweatshirt',
		'quantity' => 2,
		'price'    => 98.32,
	],

]);
```

### Remove Item

Removing items from the cart is easy, you can remove one item at a time or multiple by providing an array containing the row ids that you wish to remove.

#### Cart::remove()

Param  | Required  | Type  | Description
------ | --------- | ----- | --------------------------------------------------
$items | true      | mixed | This can be either a string or an array containing item row ids.

#### Remove a single item

```php
Cart::remove('c14c437bc9ae7d35a7c18ee151c6acc0');
```

#### Remove multiple items

```php
Cart::remove([
	'c14c437bc9ae7d35a7c18ee151c6acc0',
	'63e2d7033fe95b9134a5737503d10ba5',
]);
```

### Items

Need to show the items that are inside your shopping cart? We've you covered!

You can list all the items or grab individual items using their row ids.

#### Get all the items

```php
$items = Cart::items();

foreach ($items as $item)
{
	echo $item->price();
}
```

#### Check if an item exists

This method is most useful when deleting cart items, you can check if the item still exists on the cart before deleting it.

```php
if (Cart::exists('c14c437bc9ae7d35a7c18ee151c6acc0'))
{
	Cart::remove('c14c437bc9ae7d35a7c18ee151c6acc0');
}
```

#### Get a single item

```php
$item = Cart::item('c14c437bc9ae7d35a7c18ee151c6acc0');
```

#### Get the item price

```php
$item->price();
```

#### Get the item quantity

```php
$item->quantity();
```

#### Get the item subtotal

```php
$item->subtotal();
```

#### Get the item weight

```php
$item->weight();
```

#### Get the item attributes

```php
$item->attributes();
```

### Other Methods

In this section we're covering all the other methods that didn't fit in on the previous sections.

#### Cart::total()

Returns the cart total.

```php
echo Cart::total();
```

#### Cart::subtotal()

Returns the cart subtotal.

```php
echo Cart::subtotal();
```

#### Cart::quantity()

Returns the total number of items that are in the cart.

```php
echo Cart::quantity();
```

#### Cart::weight()

Returns the total cart weight.

```php
echo Cart::weight();
```

#### Cart::itemsSubtotal()

Get the subtotal of the items in the Cart

```php
echo Cart::itemsSubtotal();
```

#### Cart::clear()

Empty the Cart

```php
Cart::clear();
```

#### Cart::getIdentity()

Returns the cart identifier.

```php
Cart::getIdentity();
```

#### Cart::setIdentity()

Sets the cart identifier.

```php
Cart::setIdentity('my-new-cart-name');
```

#### Cart::sync()

This method is very useful when you want to synchronize a shopping cart that is stored on the database for example.

In this quick example, we're using a static array.

```php
$items = [

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

];

$collection = new Collection($items);

Cart::sync($collection);
```

### Metadata

Managing metadata inside the cart like shipping or billing information is very easy.

#### Cart::setMetadata()

Setting metadata is very easy, just provide an array with a `key`/`value` pair and you're done.

Param | Required | Type  | Description
----- | -------- | ----- | ----------------------------------------------------
$data | true     | array | Array containing the data you want to attach.

```php
$data = [
	'shipping_information' => [
		'full_name' => 'John Doe',
		'address'   => 'Example Street',
	],
];

Cart::setMetadata($data);
```

#### Cart::getMetadata()

Returning the metadata that you've set is simple.

Param | Required | Type  | Description
----- | -------- | ----- | ----------------------------------------------------
$key  | false    | mixed | The metadata key to return.

**To return all the available metadata**

```php
$metadata = Cart::getMetadata();
```

**To return metadata by keys**

```php
$metadata = Cart::getMetadata('shipping_information');
```

```php
$metadata = Cart::getMetadata('shipping_information.full_name');
```

#### Cart::removeMetadata()

Param | Required | Type  | Description
----- | -------- | ----- | ----------------------------------------------------
$key  | false    | mixed | The metadata key to remove.

**To remove all the metadata**

```php
Cart::removeMetadata();
```

**To remove metadata by keys**

```php
Cart::removeMetadata('shipping_information.full_name');
```

```php
Cart::removeMetadata('shipping_information');
```
