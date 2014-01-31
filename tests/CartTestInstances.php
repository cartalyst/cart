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
		$sessionHandler = new FileSessionHandler(new Filesystem, __DIR__.'/storage/sessions');

		$session = new IlluminateSession(new Store('cartalyst_cart_session', $sessionHandler));

		$this->cart = new Cart($session, new Dispatcher);
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

		$this->cart->add(array(
			'id'         => 'foobar1',
			'name'       => 'Foobar 1',
			'quantity'   => 2,
			'price'      => 125.00,
			'attributes' => array(
				'size'  => array(
					'label' => 'Large',
					'value' => 'l',
					'price' => 5.00,
				),
				'color' => array(
					'label' => 'Brown',
					'value' => 'brown',
					'price' => 3.50,
				),
			),
		));

		$this->assertEquals($this->cart->items()->count(), 1);

		$this->assertEquals($this->cart->total(), 267);


		$this->cart->instance('main');

		$this->assertEquals($this->cart->items()->count(), 0);

		$this->assertEquals($this->cart->total(), 0);
	}


	public function testGetAllInstances()
	{
		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 2,
			'price'    => 125.00,
		));

		$this->assertEquals($this->cart->quantity(), 2);

		$this->assertEquals($this->cart->items()->count(), 1);

		$this->assertEquals($this->cart->total(), 250);


		$this->cart->instance('wishlist');

		$this->cart->add(array(
			array(
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => 25,
				'price'    => 125.00,
			),
			array(
				'id'       => 'foobar2',
				'name'     => 'Foobar 2',
				'quantity' => 1,
				'price'    => 53.27,
			),
		));

		$this->assertEquals($this->cart->quantity(), 26);

		$this->assertEquals($this->cart->items()->count(), 2);

		$this->assertEquals($this->cart->total(), 3178.27);


		$this->cart->instance('order');

		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 5,
			'price'    => 125.00,
		));

		$this->assertEquals($this->cart->quantity(), 5);

		$this->assertEquals($this->cart->items()->count(), 1);

		$this->assertEquals($this->cart->total(), 625);


		$instances = $this->cart->instances();

		$this->assertTrue(array_key_exists('main', $instances));

		$this->assertTrue(array_key_exists('wishlist', $instances));

		$this->assertTrue(array_key_exists('order', $instances));
	}


	public function testGetCurrentInstanceName()
	{
		$this->cart->instance('wishlist');

		$this->assertEquals($this->cart->identify(), 'wishlist');
	}


	public function testFindItemsByPropertiesOnOtherCartInstances()
	{
		$this->cart->instance('wishlist');

		$this->cart->add(array(
			array(
				'id'         => 'foobar1',
				'name'       => 'Foobar 1',
				'quantity'   => 2,
				'price'      => 97.00,
				'weight'	 => 21.00,
				'attributes' => array(
					'size'  => array(
						'label' => 'Small',
						'value' => 's',
					),
					'color' => array(
						'label' => 'Red',
						'value' => 'red',
						'price' => 3.00,
					),
				),
			),
			array(
				'id'         => 'foobar2',
				'name'       => 'Foobar 2',
				'quantity'   => 2,
				'price'      => 85.00,
				'weight'     => 21.00,
				'attributes' => array(
					'size' => array(
						'label' => 'Large',
						'value' => 'l',
						'price' => 15.00,
					),
				),
			),
			array(
				'id'         => 'foobar3',
				'name'       => 'Foobar 3',
				'quantity'   => 5,
				'price'      => 35.00,
				'weight'     => 21.00,
				'attributes' => array(
					'size' => array(
						'label' => 'Large',
						'value' => 'l',
						'price' => 5.00,
					),
				),
			),
		));

		// Switch back to the main instance
		$this->cart->instance('main');

		$item = $this->cart->find(array(
			'price' => 85,
		), 'wishlist');

		$this->assertEquals($item[0]->get('id'), 'foobar2');
	}

}
