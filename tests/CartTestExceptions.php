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

class CartTestExceptions extends PHPUnit_Framework_TestCase {

	/**
	 * Holds the cart instance.
	 *
	 * @var \Cartalyst\Cart\Cart
	 */
	protected $cart;

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

		$this->cart = new Cart('cart', $session, new Dispatcher);
	}


	/**
	 * @expectedException  \Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException
	 */
	public function testThrowsCartMissingRequiredIndexException()
	{
		$this->cart->add(
			array(
				'name'     => 'abc',
				'price'    => 20.00,
				'quantity' => 5,
			)
		);
	}


	/**
	 * @expectedException  \Cartalyst\Cart\Exceptions\CartInvalidQuantityException
	 */
	public function testThrowsCartInvalidQuantityException()
	{
		$this->cart->add(
			array(
				'id'       => 1,
				'name'     => 'abc',
				'price'    => 20.00,
				'quantity' => 'dsdas',
			)
		);
	}


	/**
	 * @expectedException  \Cartalyst\Cart\Exceptions\CartInvalidPriceException
	 */
	public function testThrowsCartInvalidPriceException()
	{
		$this->cart->add(
			array(
				'id'       => 1,
				'name'     => 'abc',
				'price'    => 'dd',
				'quantity' => 5,
			)
		);
	}


	/**
	 * @expectedException  \Cartalyst\Cart\Exceptions\CartInvalidAttributesException
	 */
	public function testThrowsCartInvalidAttributesException()
	{
		$this->cart->add(
			array(
				'id'         => 1,
				'name'       => 'abc',
				'price'      => 20.00,
				'quantity'   => 5,
				'attributes' => 'abc',
			)
		);
	}


	/**
	 * @expectedException  \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function testThrowsCartItemNotFoundException()
	{
		$this->cart->remove('abc');
	}

}
