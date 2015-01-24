### Native

Integrating the package outside of a framework is incredible easy, just follow the example below.

```php
// Include the composer autoload file
require_once 'vendor/autoload.php';

// Import the necessary classes
use Cartalyst\Cart\Cart;
use Cartalyst\Cart\Storage\NativeSession;
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
$cart = new Cart('cart', $session, new Dispatcher);

$cart->setRequiredIndexes($config['requiredIndexes']);
```

The integration is done and you can now use all the available methods, here's an example:

```php
// Get all the items from the cart
$items = $cart->items();
```

> **Note 1:** Please make sure that the `storage/sessions` folder exists and has write access by the web server. This can be changed to another folder if required.

> **Note 2:** To setup garbage collection, call the `gc()` method on the FileSessionHandler `$fileSessionHandler->gc($lifetime);`, You can also setup a function that randomizes calls to this function rather than calling it on every request.
