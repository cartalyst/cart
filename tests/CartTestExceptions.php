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
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException
	 */
	public function it_throws_exception_when_missing_a_required_index()
	{
		$this->cart->add(array(
			'name'     => 'foo',
			'price'    => 20.00,
			'quantity' => 5,
		));
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException
	 */
	public function it_throws_exception_when_missing_a_required_index_on_attributes()
	{
		$this->cart->add(array(
			'id'         => 'foo',
			'name'       => 'bar',
			'price'      => 20.00,
			'quantity'   => 5,
			'attributes' => array(
				'print' => array(
					'label' => 'Bear',
				),
			),
		));
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartInvalidQuantityException
	 */
	public function it_throws_exception_when_invalid_quantity_is_passed()
	{
		$this->cart->add(array(
			'id'       => 1,
			'name'     => 'foo',
			'price'    => 20.00,
			'quantity' => 'bar',
		));
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartInvalidPriceException
	 */
	public function it_throws_exception_when_invalid_price_is_passed()
	{
		$this->cart->add(array(
			'id'       => 1,
			'name'     => 'foo',
			'price'    => 'bar',
			'quantity' => 5,
		));
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartInvalidAttributesException
	 */
	public function it_throws_exception_when_invalid_attributes_are_passed()
	{
		$this->cart->add(array(
			'id'         => 1,
			'name'       => 'foo',
			'price'      => 20.00,
			'quantity'   => 5,
			'attributes' => 'bar',
		));
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function it_throws_exception_updating_an_item_that_does_not_exist()
	{
		$this->cart->update('foo', array(
			'price' => 20.00,
		));
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function it_throws_exception_when_removing_an_item_that_does_not_exist()
	{
		$this->cart->remove('foo');
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function it_throws_exception_when_getting_an_item_that_does_not_exist()
	{
		$this->cart->item('foo');
	}

}
