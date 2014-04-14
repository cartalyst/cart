# Instances

Cart supports multiple cart instances, so that you can have as many shopping cart instances on the same page as you want without any conflicts.

You have two ways of accomplishing this, one is by creating a service provider dedicated to your wishlist to register all your other cart instances, the second method, which is easier, is to bind the new cart instances directly into the IoC.

## Example

### IoC Binding

	<?php

	use Cartalyst\Cart\Cart;
	use Cartalyst\Cart\Storage\Sessions\IlluminateSession;

	$app = app();

	$app['wishlist'] = $app->share(function($app)
	{
		$config = $app['config']->get('cartalyst/cart::config');

		$storage = new IlluminateSession($app['session.store'], $config['session_key'], 'wishlist');

		return new Cart('wishlist', $storage, $app['events']);
	});

### Create your Service Provider

`app/services/WishlistServiceProvider.php`

	<?php

	use Cartalyst\Cart\Cart;
	use Cartalyst\Cart\Storage\Sessions\IlluminateSession;
	use Illuminate\Support\ServiceProvider;

	class WishlistServiceProvider extends ServiceProvider {

		/**
		 * Register the service provider.
		 *
		 * @return void
		 */
		public function register()
		{
			$this->registerSession();

			$this->registerCart();
		}

		/**
		 * Register the session driver used by the Wishlist.
		 *
		 * @return void
		 */
		protected function registerSession()
		{
			$this->app['wishlist.storage'] = $this->app->share(function($app)
			{
				$config = $app['config']->get('cartalyst/cart::config');

				return new IlluminateSession($app['session.store'], $config['session_key'], 'wishlist');
			});
		}

		/**
		 * Register the Wishlist.
		 *
		 * @return void
		 */
		protected function registerCart()
		{
			$this->app['wishlist'] = $this->app->share(function($app)
			{
				return new Cart('wishlist', $app['wishlist.storage'], $app['events']);
			});
		}

	}

### Create your Facade

`app/facades/Wishlist.php`

	<?php

	use Illuminate\Support\Facades\Facade;

	class Wishlist extends Facade {

		protected static function getFacadeAccessor()
		{
			return 'wishlist';
		}

	}

### Register your Service Provider and Facade

Open your Laravel config file `app/config/app.php` and add the following lines.

In the `$providers` array add the following service provider for this package.

	'Path\To\Your\CartServiceProvider',

In the `$aliases` array add the following facade for this package.

	'Wishlist' => 'Path\To\Your\Wishlist',

### Usage

Usage is identical to the cart.

	Wishlist::add([
			'id'       => 'foobar1',
			'name'     => 'Foo Bar 1',
			'quantity' => 1,
			'price'    => 12.50,
	]);
