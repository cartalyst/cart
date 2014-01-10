# Installation

The best way to install Intervention Image is quickly and easily with [Composer](http://getcomposer.org).

Open your `composer.json` and add the following to the requires array

	"cartalyst/cart": "1.0.*"

Add the following lines after the `require` array on your `composer.json` file

	"repositories": [
		{
			"type": "composer",
			"url": "http://packages.cartalyst.com"
		}
	]

**Note:** Make sure your composer.json` file is valid in the end.


Run Composer to install or update the new requirement.

	php composer install

or

	php composer update

Now you are able to require the vendor/autoload.php file to PSR-0 autoload the library.

## Example

	// Include the composer autoload file
	require 'vendor/autoload.php';

	// Import the Cart class
	use Cartalyst\Cart\Cart;

	.. more stuff required here

	// Instantiate a new Cart
	$cart = new Cart

The package also has optional Laravel 4 support. The integration into the framework is done in seconds.

Read more about the [Laravel 4 integration]({url}/introduction/laravel-4).
