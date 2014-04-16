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
use Cartalyst\Cart\Storage\IlluminateSession;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\FileSessionHandler;
use Illuminate\Session\Store;
use Mockery as m;

class CartTestEvents extends CartTestCase {

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

	/** @test */
	public function can_listen_to_the_added_event()
	{
		$this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.added', m::any());

		$item = $this->createItem('Foobar 1', 125, 2);

		$this->cart->add($item);
	}

	/** @test */
	public function can_listen_to_the_updated_event()
	{
		$this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.added', m::any());

		$this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.updated', m::any());

		$item = $this->createItem('Foobar 1', 125, 2);

		$this->cart->add($item);

		$this->cart->update('b37f673e46a33038305c1dc411215c07', [
			'name' => 'Foo',
		]);
	}

	/** @test */
	public function can_listen_to_the_removed_event()
	{
		$this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.added', m::any());

		$this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.removed', m::any());

		$item = $this->createItem('Foobar 1', 125, 2);

		$this->cart->add($item);

		$this->cart->remove('b37f673e46a33038305c1dc411215c07');
	}

	/** @test */
	public function can_listen_to_the_cleared_event()
	{
		$this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.cleared', m::any());

		$this->cart->clear();
	}

}
