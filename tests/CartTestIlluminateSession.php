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

use Cartalyst\Cart\Storage\IlluminateSession;
use Mockery as m;

class CartTestIlluminateSession extends CartTestCase {

	/**
	 * Close mockery.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		m::close();
	}

	/** @test */
	public function it_can_get_cart_session_key_and_cart_identity()
	{
		$this->assertTrue($this->cart->getStorage() instanceof IlluminateSession);
		$this->assertEquals($this->cart->getStorage()->getKey(), 'cartalyst_cart');
		$this->assertEquals($this->cart->getStorage()->identify(), 'main');

		$item = $this->createItem('Foobar 1', 125, 2);

		$this->cart->add($item);

		$this->assertEquals($this->cart->items()->count(), 1);

		$this->cart->getStorage()->forget();

		$this->assertEquals($this->cart->items()->count(), 0);
	}

	/** @test */
	public function it_can_set_cart_session_key_and_cart_identity_on_initialization()
	{
		$session = new IlluminateSession(m::mock('Illuminate\Session\Store'), 'cart', 'instance');

		$this->assertEquals($session->getKey(), 'cart');
		$this->assertEquals($session->identify(), 'instance');
	}

}
