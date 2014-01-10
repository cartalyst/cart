# Installation

The best way to install the Cart package is quickly and easily done with [Composer](http://getcomposer.org).

Open your `composer.json` and add the following to the require array

	"cartalyst/cart": "1.0.*"

Add the following lines after the `require` array on your `composer.json` file

	"repositories": [
		{
			"type": "composer",
			"url": "http://packages.cartalyst.com"
		}
	]

**Note:** Make sure your `composer.json` file is in a valid JSON format after the required changes.

Run Composer to install or update the new requirement.

	php composer install

or

	php composer update

Now you are able to require the `vendor/autoload.php` file to PSR-0 autoload the library.

## Example

	// Include the composer autoload file
	require_once 'vendor/autoload.php';

	// Import the necessary classes
	use Cartalyst\Cart\Cart;
	use Cartalyst\Cart\Storage\Sessions\IlluminateSession;
	use Illuminate\Events\Dispatcher;
	use Illuminate\Filesystem\Filesystem;
	use Illuminate\Session\FileSessionHandler;
	use Illuminate\Session\Store;

	// Require the cart config file
	$config = require_once 'vendor/cartalyst/cart/src/config/config.php';

	// Instantiate a new Session storage
	$fileSessionHandler = new FileSessionHandler(new Filesystem(), __DIR__.'/storage/sessions');

	$store = new Store('cartalyst_cart_session', $fileSessionHandler);

	$session = new IlluminateSession($store);

	// Instantiate the Cart and set the necessary configuration
	$cart = new Cart($session, new Dispatcher);
	$cart->instance($config['instance']);
	$cart->setRequiredIndexes($config['requiredIndexes']);

	// Get all the items on the cart
	$items = $cart->items();


The package also has optional Laravel 4 support. The integration into the framework is done in seconds.

Read more about the [Laravel 4 integration]({url}/introduction/laravel-4).
