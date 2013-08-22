<?php namespace Cartalyst\Cart\ServiceProviders;
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
use Cartalyst\Cart\Storage\DatabaseStorage;
use Cartalyst\Cart\Storage\SessionStorage;
use Illuminate\Support\ServiceProvider;

class Laravel extends ServiceProvider {

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

		$this->registerSessionStorage();

		$this->app['cart'] = $this->app->share(function($app)
		{
			return new Cart($app['cart.session'], $app['config']);
		});
	}

	/**
	 * Register the session driver used by Cart.
	 *
	 * @return void
	 */
	protected function registerSessionStorage()
	{
		$this->app['cart.session'] = $this->app->share(function($app)
		{
			// Get the key name
			$key = $app['config']->get('cart::session.key');

			// Get the default instance
			$instance = $app['config']->get('cart::instance');

			return new SessionStorage($app['session'], $key, $instance);
		});
	}

}
