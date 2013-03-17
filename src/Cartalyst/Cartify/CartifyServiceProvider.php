<?php namespace Cartalyst\Cartify;

use Illuminate\Support\ServiceProvider;
use Cartalyst\Cartify\Sessions\IlluminateSession;
use Cartalyst\Cartify\Cart\Cart;

class CartifyServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerSession();

		$this->registerCartify();
	}

	/**
	 * Register the session driver used by Cartify.
	 *
	 * @return void
	 */
	protected function registerSession()
	{
		$this->app['cartify.session'] = $this->app->share(function($app)
		{
			return new IlluminateSession($app['session']);
		});
	}

	/**
	 * Takes all the components of Cartify and glues them
	 * all together to create Cartify.
	 *
	 * @return void
	 */
	protected function registerCartify()
	{
		$this->app['cartify\cart'] = $this->app->share(function($app){

			return new Cart($app['cartify.session']);

		});

		$this->app['cartify\payment'] = $this->app->share(function($app){

			return new Payment;

		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('cartify');
	}

}
