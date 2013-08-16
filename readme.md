Cartify v1
===============

Cartify is a framework agnostic shopping cart package.

------

### Installing In Laravel 4 (with Composer)

There are four simple steps to install Cartify into Laravel 4:

#### Step 1

Open your `composer.json` and add to the `required` attribute the following line:

	"cartalyst/cartify": "1.0.*"

#### Step 2

Run `composer update` from the command line.

#### Step 3

Add the following to the list of service providers in `app/config/app.php`

	Cartalyst\Cartify\ServiceProviders\Laravel

#### Step 4

Add the following to the list of class aliases in `app/config/app.php`

	'Cart' => 'Cartalyst\Cartify\Facades\Laravel',

------

## Usage

### Adding items

**Add a single item to the cart**

```php
try
{
	Cart::add('foobar123', 'Foo Bar 123', 1, 12.50);
}
catch (Cartalyst\Cartify\Exceptions\CartInvalidDataException $e)
{
	echo 'Missing a required parameter.';
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
		),
	));
}
catch (Cartalyst\Cartify\Exceptions\CartInvalidDataException $e)
{
	echo 'The provided array is malformed.';
}
```

### Removing items

**Remove a single item**

```php
try
{
	Cart::remove('027c91341fd5cf4d2579b49c4b6a90da');
}
catch (Cartalyst\Cartify\Exceptions\CartItemNotfoundException $e)
{
	echo 'Item was not found.';
}
```

**Remove multiple items**

Removing multiple items is easy and we provide you with two ways to accomplish this.

##### Method 1

Pass in an array with the row ids you want to delete.

```php
try
{
	Cart::remove(array(
		'027c91341fd5cf4d2579b49c4b6a90da',
		'56f0ab12a38f8317060d40981f6a4a93',
	));
}
catch (Cartalyst\Cartify\Exceptions\CartItemNotfoundException $e)
{
	echo 'One of the provided items was not found.';
}
```

##### Method 2

Pass in multiple arguments, where each argument correspond to an item row id.

```php
try
{
	Cart::remove('027c91341fd5cf4d2579b49c4b6a90da', '56f0ab12a38f8317060d40981f6a4a93');
}
catch (Cartalyst\Cartify\Exceptions\CartItemNotfoundException $e)
{
	echo 'One of the provided items was not found.';
}
```

### Updating items

**Update an item quantity**

```php
try
{
	Cart::update('027c91341fd5cf4d2579b49c4b6a90da', 2);
}
catch (Cartalyst\Cartify\Exceptions\CartItemNotFoundException $e)
{
	echo 'Item was not found.';
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
catch (Cartalyst\Cartify\Exceptions\CartItemNotFoundException $e)
{
	echo 'Item was not found.';
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
catch (Cartalyst\Cartify\Exceptions\CartItemNotFoundException $e)
{
	echo 'One of the provided items was not found.';
}
```
