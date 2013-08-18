<?php namespace Cartalyst\Cartify\ServiceProviders;
/**
 * Part of the Cartify package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Cartify
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Cartify\Cartify;
use Cartalyst\Cartify\Storage\SessionStorage;
use Illuminate\Support\ServiceProvider;

class Laravel extends ServiceProvider {

	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('cartalyst/cartify', 'cartalyst/cartify');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['config']->package('cartalyst/cartify', __DIR__.'/../../config');

		$this->app['cartify.storage'] = $this->app->share(function($app)
		{
			$sessionKey = $app['config']->get('cartify::session');

			return new SessionStorage($app['session'], $sessionKey);
		});

		$this->app['cartify'] = $this->app->share(function($app)
		{
			return new Cartify($app['cartify.storage'], $app['config']);
		});
	}

}
