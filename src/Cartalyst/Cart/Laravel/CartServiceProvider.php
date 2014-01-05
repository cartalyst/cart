<?php namespace Cartalyst\Cart\Laravel;
/**
 * Part of the Cart package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Cart
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Cart\Cart;
use Cartalyst\Cart\Storage\Cookies\IlluminateCookie;
use Cartalyst\Cart\Storage\Database\IlluminateDatabase;
use Cartalyst\Cart\Storage\Sessions\IlluminateSession;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider {

	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('cartalyst/cart', 'cartalyst/cart');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['config']->package('cartalyst/cart', __DIR__.'/../../config');

		//$this->registerDatabase();

		$this->registerSession();

		$this->registerCart();
	}

	/**
	 * Register the database driver used by the Cart.
	 *
	 * @return void
	 */
	protected function registerDatabase()
	{
		$this->app['cart.storage.database'] = $this->app->share(function($app)
		{
			// Get the key name
			$key = $app['config']->get('cart::session.key');

			// Get the default instance
			$instance = $app['config']->get('cart::instance');

			return new IlluminateDatabase($app['db'], $app['cookie'], $key, $instance);
		});
	}

	/**
	 * Register the session driver used by the Cart.
	 *
	 * @return void
	 */
	protected function registerSession()
	{
		$this->app['cart.storage.session'] = $this->app->share(function($app)
		{
			// Get the key name
			$key = $app['config']->get('cart::session.key');

			// Get the default instance
			$instance = $app['config']->get('cart::instance');

			return new IlluminateSession($app['session.store'], $key, $instance);
		});
	}

	/**
	 * Register the Cart.
	 *
	 * @return void
	 */
	protected function registerCart()
	{
		$this->app['cart'] = $this->app->share(function($app)
		{
			// Get the default storage driver
			$storage = $app['config']->get('cart::driver', 'session');

			// Create a new Cart instance
			$cart = new Cart($app["cart.storage.{$storage}"], $app['events']);

			// Set the default cart instance
			$cart->instance($app['config']->get('cart::instance', 'main'));

			// Set the required indexes
			$cart->setRequiredIndexes($app['config']->get('cart::requiredIndexes'));

			return $cart;
		});
	}

}
