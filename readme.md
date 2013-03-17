Cartify v3
===============

Cartify is a framework agnostic *(not yet)* shopping cart package.

------

### Installing In Laravel 4 (with Composer)

There are four simple steps to install Cartify into Laravel 4:

#### Step 1

Open your `composer.json` and add to the `required` attribute the following line:

	"cartalyst/cartify": "3.0.*"

#### Step 2

Run `php composer.phar update` from the command line.

#### Step 3

Add the following to the list of service providers in `app/config/app.php`

	Cartalyst\Cartify\SentryServiceProvider

#### Step 4

Add the following to the list of class aliases in `app/config/app.php`

	'Cartify\Cart' => 'Cartalyst\Cartify\Facades\Laravel\Cart',

------

### Adding items to the shopping cart

	try
	{
		Cartify\Cart::insert($item);
	}
	catch (Cartalyst\Cartify\InvalidDataException $e)
	{
		echo 'The provided array is malformed.';
	}
	catch (Cartalyst\Cartify\RequiredIndexException $e)
	{
		echo $e->getMessage();
	}
	catch (Cartalyst\Cartify\InvalidItemQuantityException $e)
	{
		echo 'The provided item quantity is invalid.';
	}
	catch (Cartalyst\Cartify\InvalidItemIdException $e)
	{
		echo 'The provided item id is invalid.';
	}
	catch (Cartalyst\Cartify\InvalidItemPriceException $e)
	{
		echo 'The provided item price is invalid.';
	}


### Remove an item

	try
	{
		Cartify\Cart::remove($itemRowId);
	}
	catch (Cartalyst\Cartify\ItemNotfoundException $e)
	{
		echo 'Item was not found.';
	}


### Update an item

	try
	{
		Cartify\Cart::update($itemRowId, $itemData);
	}
	catch (Cartalyst\Cartify\InvalidDataException $e)
	{
		echo 'The provided array is malformed.';
	}
	catch (Cartalyst\Cartify\ItemNotFoundException $e)
	{
		echo 'Item was not found.';
	}


