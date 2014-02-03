# Instances

Cart supports multiple cart instances, so that you can have as many shopping carts instances on the same page as you want without any conflicts.

<!--
In order for you to understand the usage of instances, we recommend you
to have a read on [Facades](http://laravel.com/docs/facades) and
[Service Providers](http://laravel.com/docs/ioc#service-providers) first so
that you can have a better understanding on how it really works.
-->


You have two ways of accomplishing this, one is by creating a service provider dedicated to your wishlist or if
required, to register all your other carts, the second method, which is easier, is to bind the new "cart" directly
into the IoC.


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


### Register your Service Provider


### Register your Facade


### Usage
