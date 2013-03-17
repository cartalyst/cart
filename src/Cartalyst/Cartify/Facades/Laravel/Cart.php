<?php namespace Cartalyst\Cartify\Facades\Laravel;

use Illuminate\Support\Facades\Facade;

class Cart extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'cartify\cart';
	}

}
