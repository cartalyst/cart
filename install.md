### Installing In Laravel 4 (with Composer)

There are four simple steps to install Cart into Laravel 4:

#### Step 1

Open your `composer.json` and add to the `required` attribute the following line:

	"cartalyst/cart": "1.0.*"

#### Step 2

Run `composer update` from the command line.

#### Step 3

Add the following to the list of service providers in `app/config/app.php`

	Cartalyst\Cart\ServiceProviders\Laravel

#### Step 4

Add the following to the list of class aliases in `app/config/app.php`

	'Cart' => 'Cartalyst\Cart\Facades\Laravel',

#### Step 5 (Optional)

Publish the config file to change the default settings.

Run `php artisan config:publish cartalyst/cart`

------

## Usage

### Adding items

**Add a single item to the cart**

```php
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
```

**Add multiple items into the cart**

```php
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
```

> Note: We have optional parameteres like `tax` and `weight` that you can pass
when adding a product into the cart.

### Removing items

**Remove a single item**

```php
try
{
	Cart::remove('027c91341fd5cf4d2579b49c4b6a90da');
}
catch (Cartalyst\Cart\Exceptions\CartItemNotfoundException $e)
{
	die('Item was not found.');
}
```

**Remove multiple items**

Removing multiple items is easy and we provide you with two ways to accomplish this.

##### Method 1

Pass in an array with the row id's you want to remove.

```php
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
```

##### Method 2

Pass in multiple arguments, where each argument corresponds to an item row id.

```php
try
{
	Cart::remove('027c91341fd5cf4d2579b49c4b6a90da', '56f0ab12a38f8317060d40981f6a4a93');
}
catch (Cartalyst\Cart\Exceptions\CartItemNotfoundException $e)
{
	die('One of the provided items was not found.');
}
```

### Updating items

**Update an item quantity**

```php
try
{
	Cart::update('027c91341fd5cf4d2579b49c4b6a90da', 2);
}
catch (Cartalyst\Cart\Exceptions\CartItemNotFoundException $e)
{
	die('Item was not found.');
}
```

**Update a single item**

```php
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
```

**Update multiple items**

```php
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
```

## Other methods

**Grab information of an Item**

```php
try
{
	$item = Cart::getItem('027c91341fd5cf4d2579b49c4b6a90da');
}
catch (Cartalyst\Cart\Exceptions\CartItemNotFoundException $e)
{
	die('The item was not found.');
}
```

**Get the Cart contents**

```php
$content = Cart::getContent();
```

**Destroy or Empty the Cart completely**

```php
Cart::destroy();
```

**Get the Total of the Cart**

```php
$total = Cart::getTotal();
```

**Get the Total of items that are in the Cart**

```php
$totalItems = Cart::getTotalItems();
```

**Search for Items**

```php
Cart::find(array(
	'id'    => 'foobar',
	'name'  => 'Foo Bar',
	'price' => (float) 5,
));
```

**Search for Items on other Cart Instances**

```php
Cart::find(array(
	'id'   => 'foobar',
	'name' => 'Foo Bar',
), 'wishlist');
```

**Search for Items with attributes**
```php
Cart::find(array(
	'id'   => 'foobar',
	'name' => 'Foo Bar',
	'attributes' => array(
		'size' => array(
			'price' => (float) 5,
		),
	),
));
```
> Note: When searching for `price` or `quantity` make sure that the value is a `float`.

## Instances

Cart supports multiple cart instances, so that this way you can have has
many shopping carts on the same page as you want without any conflicts.

Here are some examples on how it works

**Wishlist cart**

On this example i will create a wishlist cart, where it will hold all of our
customers wishlist items.

```php
Cart::instance('wishlist')->add(array(...));

$content = Cart::getContent();
```

As you can see the main difference was that we used the `instance()` method before
adding the item, this way Cart knows where we want to save the item.

You probably have noticed that, on the `getContent()` call we are not using the
`instance()` method anymore, thats because once you use the `instance()`
 method, the other calls will use the instance you used before.

You are probably wondering, how you would go back to the normal cart instance,
it's very easy, just use `instance()` method again, and pass in the cart
instance name, and you should be good to go, example:

```php
Cart::instance('main');

$content = Cart::getContent();
```

Now you are able to get the main cart content.

**Grabbing all the Instances**

```php
Cart::getInstances()
```

**Remove an Instance**

In order to remove an instance, you first need to set the instance you want to
remove as the active one, then use the `forgetInstance()` method to remove it.

```php
Cart::instance('wishlist');

Cart::forgetInstance();
```
