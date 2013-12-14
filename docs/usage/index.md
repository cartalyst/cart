## Usage

&nbsp;

### Adding items {#add-items}

---

#### Add a single item to the cart

	try
	{
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
	}
	catch (Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException $e)
	{
		die("The [{$e->getMessage()}] value is required.");
	}
	catch (Cartalyst\Cart\Exceptions\CartInvalidQuantityException $e)
	{
		die('Quantity is invalid.');
	}
	catch (Cartalyst\Cart\Exceptions\CartInvalidPriceException $e)
	{
		die('Price is invalid.');
	}
	catch (Cartalyst\Cart\Exceptions\CartInvalidAttributesException $e)
	{
		die('The provided attributes array is invalid or malformed.');
	}

#### Add multiple items into the cart

	try
	{
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
	}
	catch (Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException $e)
	{
		die("The [{$e->getMessage()}] value is required.");
	}

> **Note:** We have optional parameteres like `tax` and `weight` that you can pass
	when adding a product into the cart.

### Removing items {#remove-items}

---

#### Remove a single item

	try
	{
		Cart::remove('027c91341fd5cf4d2579b49c4b6a90da');
	}
	catch (Cartalyst\Cart\Exceptions\CartItemNotfoundException $e)
	{
		die('Item was not found.');
	}


#### Remove multiple items

Removing multiple items is easy and we provide you with two ways to accomplish this.

##### Method 1

Pass in an array with the row id's you want to remove.

	try
	{
		Cart::remove(array(
			'027c91341fd5cf4d2579b49c4b6a90da',
			'56f0ab12a38f8317060d40981f6a4a93',
		));
	}
	catch (Cartalyst\Cart\Exceptions\CartItemNotfoundException $e)
	{
		die('One of the provided items was not found.');
	}

##### Method 2

Pass in multiple arguments, where each argument corresponds to an item row id.

	try
	{
		Cart::remove('027c91341fd5cf4d2579b49c4b6a90da', '56f0ab12a38f8317060d40981f6a4a93');
	}
	catch (Cartalyst\Cart\Exceptions\CartItemNotfoundException $e)
	{
		die('One of the provided items was not found.');
	}

### Updating items {#update-items}

---

#### Update an item quantity

	try
	{
		Cart::update('027c91341fd5cf4d2579b49c4b6a90da', 2);
	}
	catch (Cartalyst\Cart\Exceptions\CartItemNotFoundException $e)
	{
		die('Item was not found.');
	}

#### Update a single item

	try
	{
		Cart::update('027c91341fd5cf4d2579b49c4b6a90da', array(
			'id'       => 'foobar123',
			'name'     => 'Foo Bar 123',
			'quantity' => 1,
			'price'    => 12.50,
		));
	}
	catch (Cartalyst\Cart\Exceptions\CartItemNotFoundException $e)
	{
		die('Item was not found.');
	}

#### Update multiple items

	try
	{
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
	}
	catch (Cartalyst\Cart\Exceptions\CartItemNotFoundException $e)
	{
		die('One of the provided items was not found.');
	}

### Other methods {#other-methods}

---

#### Grab information of an Item

	try
	{
		$item = Cart::item('027c91341fd5cf4d2579b49c4b6a90da');
	}
	catch (Cartalyst\Cart\Exceptions\CartItemNotFoundException $e)
	{
		die('The item was not found.');
	}

#### Get all items in the Cart

	$content = Cart::items();

#### Destroy Cart instance completely

	Cart::destroy();

#### Empty Cart instance

	Cart::clear();

#### Get the Cart Subtotal

	$subtotal = Cart::subtotal();


#### Get the Cart Total


	$total = Cart::total();


#### Get Total number of items in the Cart


	$quantity = Cart::quantity();


#### Search for Items

You can use one or multiple properties to search for items in the cart

	Cart::find(array(
		'id'    => 'foobar',
		'name'  => 'Foo Bar',
		'price' => (float) 5,
	));


#### Search for Items on other Cart Instances

	Cart::find(array(
		'id'   => 'foobar',
		'name' => 'Foo Bar',
	), 'wishlist');


#### Search for Items with attributes

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

### Instances {#instances}

---

Cart supports multiple cart instances, so that you can have as many shopping carts on the same page as you want without any conflicts.

Here are some examples on how it works

#### Wishlist cart

On this example i will create a wishlist cart, it will hold all of our customers wishlist items.

	Cart::instance('wishlist')->add(array(...));

	$content = Cart::items();

As you can see the main difference was that we used the `instance()` method before
adding the item, this way Cart knows where we want to save the item.

You probably have noticed that, on the `items()` call we are not using the `instance()` method anymore, thats because once you use the `instance()` method, the other calls will use the instance you used last.

You are probably wondering, how would i go back to the normal cart instance? it's very easy, just use `instance()` method again, and pass in the cart instance name ( default is `main` ), and you should be good to go, example:

	Cart::instance('main');

	$content = Cart::items();

Now you are able to get the main cart content.

#### Grabbing all instances

	Cart::instances()
