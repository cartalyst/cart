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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Cart\Cart;
use Cartalyst\Conditions\Condition;
use Illuminate\Events\Dispatcher;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class CartTestInstances extends PHPUnit_Framework_TestCase {

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
		$filesystem = new \Illuminate\Filesystem\Filesystem();
		$fileSessionHandler = new \Illuminate\Session\FileSessionHandler(
			$filesystem,
			__DIR__ . '/storage/sessions'
		);

		$store = new \Illuminate\Session\Store(
			'cartalyst_cart_session',
			$fileSessionHandler
		);

		$session = new \Cartalyst\Cart\Storage\Sessions\IlluminateSession($store);

		$dispatcher = new Dispatcher;

		$this->cart = new Cart($session, $dispatcher);
	}



	public function testCanSwitchInstance()
	{
		$cart = m::mock('cart');
		$cart->shouldReceive('instance')->once();

		$cart->instance();
	}

	public function testAddItemToAnotherInstance()
	{
		$this->cart->instance('wishlist');

		$this->cart->add(
			array(
				'id'         => 'foobar1',
				'name'       => 'Foobar 1',
				'quantity'   => 2,
				'price'      => 125.00,
				'attributes' => array(
					'size'  => array(
						'label' => 'Size',
						'value' => 'L',
						'price' => 5.00,
					),
					'color' => array(
						'label' => 'Size',
						'value' => 'L',
						'price' => 3.00,
					),
				),
			)
		);

		$this->assertEquals($this->cart->items()->count(), 1);

		$this->assertEquals($this->cart->total(), 266);
	}


	public function testGetAllInstances()
	{
		$this->cart->add(
			array(
				'id' => 1,
				'price' => 20.00,
				'name' => 'foobar',
				'quantity' => 1,
			)
		);

		$this->cart->instance('wishlist');

		$this->cart->add(
			array(
				'id' => 1,
				'price' => 20.00,
				'name' => 'foobar',
				'quantity' => 1,
			)
		);

		$this->cart->instance('order');

		$this->cart->add(
			array(
				array(
					'id' => 1,
					'price' => 20.00,
					'name' => 'foobar',
					'quantity' => 1,
				)
			)
		);

		$this->assertTrue(array_key_exists('main', $this->cart->instances()));

		$this->assertTrue(array_key_exists('wishlist', $this->cart->instances()));

		$this->assertTrue(array_key_exists('order', $this->cart->instances()));
	}


	public function testGetCurrentInstanceName()
	{
		$this->cart->instance('wishlist');

		$this->assertEquals($this->cart->identify(), 'wishlist');
	}

}
