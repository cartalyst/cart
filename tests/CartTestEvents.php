<?php namespace Cartalyst\Cart\Tests;
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
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\FileSessionHandler;
use Illuminate\Session\Store;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class CartTestEvents extends PHPUnit_Framework_TestCase {

	/**
	 * Holds the cart instance.
	 *
	 * @var \Cartalyst\Cart\Cart
	 */
	protected $cart;

	/**
	 * Holds the dispatcher instance.
	 *
	 * @var \Illuminate\Events\Dipstacher
	 */
	protected $dispatcher;

	/**
	 * Close mockery.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		m::close();
	}

	/**
	 * Setup resources and dependencies
	 */
	public function setUp()
	{
		$sessionHandler = new FileSessionHandler(new Filesystem, __DIR__.'/storage/sessions');

		$session = new IlluminateSession(new Store('cartalyst_cart_session', $sessionHandler));

		$this->dispatcher = m::mock('Illuminate\Events\Dispatcher');

		$this->cart = new Cart('cart', $session, $this->dispatcher);
	}


	public function testAddItemEvent()
	{
		$this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.added', m::any());

		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 2,
			'price'    => 125.00,
		));
	}


	public function testUpdateItemEvent()
	{
		$this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.added', m::any());

		$this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.updated', m::any());

		$this->cart->add(array(
			'id'       => 'foobar2',
			'name'     => 'Foobar 2',
			'quantity' => 3,
			'price'    => 120.00,
		));

		$this->cart->update('2d2d8cb241842b326ce0e095dbfc4d41', array(
			'name' => 'Foo',
		));
	}


	public function testDeleteItemEvent()
	{
		$this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.added', m::any());

		$this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.removed', m::any());

		$this->cart->add(array(
			'id'       => 'foobar2',
			'name'     => 'Foobar 2',
			'quantity' => 3,
			'price'    => 120.00,
		));

		$this->cart->remove('2d2d8cb241842b326ce0e095dbfc4d41');
	}


	public function testClearCartEvent()
	{
		$this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.cleared', m::any());

		$this->cart->clear();
	}


}
