# Installation

The best and easy way to install the Cart package is with [Composer](http://getcomposer.org).

### Preparation

Open your `composer.json` and add the following to the `require` array:

	"cartalyst/cart": "1.0.*"

Add the following lines after the `require` array on your `composer.json` file:

	"repositories": [
		{
			"type": "composer",
			"url": "http://packages.cartalyst.com"
		}
	]

> **Note 1:** Change the `"minimum-stability"` flag to `"dev"` `"minimum-stability" : "dev"` as at
this point the package is not marked as stable.

> **Note 2:** Make sure your `composer.json` file is in a valid JSON format after the required changes.

### Install the dependencies

Run Composer to install or update the new requirement.

	php composer install

or

	php composer update

Now you are able to require the `vendor/autoload.php` file to autoload the package.

## Example

	// Include the composer autoload file
	require_once 'vendor/autoload.php';

	// Import the necessary classes
	use Cartalyst\Cart\Cart;
	use Cartalyst\Cart\Storage\Sessions\NativeSession;
	use Illuminate\Events\Dispatcher;
	use Illuminate\Filesystem\Filesystem;
	use Illuminate\Session\FileSessionHandler;
	use Illuminate\Session\Store;

	// Require the cart config file
	$config = require_once 'vendor/cartalyst/cart/src/config/config.php';

	// Instantiate a new Session storage
	$fileSessionHandler = new FileSessionHandler(new Filesystem(), __DIR__.'/storage/sessions');

	$store = new Store('your_app_session_name', $fileSessionHandler);

	$session = new NativeSession($store, $config['session_key'], $config['instance']);

	// Instantiate the Cart and set the necessary configuration
	$cart = new Cart($config['instance'], $session, new Dispatcher);

	$cart->setRequiredIndexes($config['requiredIndexes']);

	// Get all the items from the cart
	$items = $cart->items();

> **Note 1:** Please make sure that the `storage/sessions` folder exists and has write access by the web server. This can be changed to other folder structure if required.

> **Note 2:** To setup garbage collection, call the gc method on the FileSessionHandler `$fileSessionHandler->gc($seconds);`, You can also setup a function that randomizes calls to this function rather than calling it on every request.

The package also has optional Laravel 4 support. The integration into the framework is done in seconds.

Read more about the [Laravel 4 integration]({url}/introduction/laravel-4).
