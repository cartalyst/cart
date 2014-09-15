<?php namespace Cartalyst\Cart\Laravel;
/**
 * Part of the Cart package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Cart
 * @version    1.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Cart\Cart;
use Cartalyst\Cart\Storage\IlluminateSession;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		$this->package('cartalyst/cart', 'cartalyst/cart', __DIR__.'/..');
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		$this->registerSession();

		$this->registerCart();
	}

	/**
	 * {@inheritDoc}
	 */
	public function provides()
	{
		return [
			'cart',
			'cart.session',
		];
	}

	/**
	 * Register the session driver used by the Cart.
	 *
	 * @return void
	 */
	protected function registerSession()
	{
		$this->app['cart.session'] = $this->app->share(function($app)
		{
			$config = $app['config']->get('cartalyst/cart::config');

			return new IlluminateSession($app['session.store'], $config['session_key'], $config['instance']);
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
			$config = $app['config']->get('cartalyst/cart::config');

			$cart = new Cart($config['instance'], $app['cart.session'], $app['events']);

			$cart->setRequiredIndexes($config['requiredIndexes']);

			return $cart;
		});

		$this->app->alias('cart', 'Cartalyst\Cart\Cart');
	}

}
