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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Cart\Cart;
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
		$this->package('cartalyst/cart', 'cartalyst/cart', __DIR__.'/../../..');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['cart'] = $this->app->share(function($app)
		{
			// Get the Cart config
			$config = $app['config']->get('cartalyst/cart::config');

			// Get the default instance
			$instance = $config['instance'];

			// Create a new Session instance
			$session = new IlluminateSession($app['session.store'], $config['session_key'], $instance);

			// Create a new Cart instance
			$cart = new Cart($session, $app['events']);

			// Set the default cart instance
			$cart->instance($instance);

			// Set the required indexes
			$cart->setRequiredIndexes($config['requiredIndexes']);

			return $cart;
		});
	}

}
