<?php namespace Cartalyst\Cart\Tests\Crud;
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

class Adding extends PHPUnit_Framework_TestCase {

	/**
	 * Holds the Cart instance.
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
		$sessionHandler = new FileSessionHandler(new Filesystem, __DIR__ . '/storage/sessions');

		$session = new IlluminateSession(new Store('cartalyst_cart_session', $sessionHandler));

		$this->cart = new Cart('cart', $session, new Dispatcher);
	}


	public function testAddItem()
	{
		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 2,
			'price'    => 10.00,
		));

		$this->assertEquals($this->cart->quantity(), 2);

		$this->assertEquals($this->cart->items()->count(), 1);
	}


	public function testAddItemWithQuantityAsString()
	{
		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => '0000002',
			'price'    => 10.00,
		));

		$this->assertEquals($this->cart->quantity(), 2);
	}


	public function testAddItemWithPriceAsString()
	{
		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 2,
			'price'    => '10.00',
		));

		$item = $this->cart->items()->first();

		$this->assertEquals($item->get('price'), 10);
	}


	/**
	 * @expectedException \Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException
	 */
	public function testAddItemWithMissingPrice()
	{
		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 2,
		));
	}


	/**
	 * @expectedException \Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException
	 */
	public function testAddItemWithMissingQuantity()
	{
		$this->cart->add(array(
			'id'    => 'foobar1',
			'name'  => 'Foobar 1',
			'price' => 10.00,
		));
	}


	/**
	 * @expectedException \Cartalyst\Cart\Exceptions\CartInvalidQuantityException
	 */
	public function testAddItemWithInvalidQuantity()
	{
		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => -2,
			'price'    => 125.00,
		));
	}


	/**
	 * @expectedException \Cartalyst\Cart\Exceptions\CartInvalidPriceException
	 */
	public function testAddItemWithInvalidPrice()
	{
		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 1,
			'price'    => 'foo',
		));
	}


	public function testAddMultipleItems()
	{
		$this->cart->add(array(
			array(
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => 3,
				'price'    => 4,
			),
			array(
				'id'       => 'foobar2',
				'name'     => 'Foobar 2',
				'quantity' => 2,
				'price'    => 21.00,
			),
			array(
				'id'       => 'foobar3',
				'name'     => 'Foobar 3',
				'quantity' => 2,
				'price'    => 120.00,
			),
		));

		$this->assertEquals($this->cart->items()->count(), 3);

		$this->assertEquals($this->cart->quantity(), 7);

		$this->assertEquals($this->cart->total(), 294);
	}


	/**
	 * @expectedException \Cartalyst\Cart\Exceptions\CartInvalidQuantityException
	 */
	public function testAddMultipleItemsWithOneHavingInvalidQuantity()
	{
		$this->cart->add(array(
			array(
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => '03',
				'price'    => 4,
			),
			array(
				'id'       => 'foobar2',
				'name'     => 'Foobar 2',
				'quantity' => -5,
				'price'    => 21.00,
			),
			array(
				'id'       => 'foobar3',
				'name'     => 'Foobar 3',
				'quantity' => 2,
				'price'    => 120.00,
			),
		));
	}

	/**
	 * @expectedException \Cartalyst\Cart\Exceptions\CartInvalidPriceException
	 */
	public function testAddMultipleItemsWithOneHavingInvalidPrice()
	{
		$this->cart->add(array(
			array(
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => '03',
				'price'    => 4,
			),
			array(
				'id'       => 'foobar2',
				'name'     => 'Foobar 2',
				'quantity' => 5,
				'price'    => 'foo',
			),
			array(
				'id'       => 'foobar3',
				'name'     => 'Foobar 3',
				'quantity' => 2,
				'price'    => 120.00,
			),
		));
	}


	public function testAddExistingItemToUpdateTheQuantity()
	{
		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 3,
			'price'    => 4,
		));

		$item = $this->cart->items()->first();

		$this->assertEquals($item->get('quantity'), 3);

		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 6,
			'price'    => 4,
		));

		$this->assertEquals($item->get('quantity'), 9);
	}


	public function testAddItemWithAttributes()
	{
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
					'label' => 'Red',
					'value' => 'red',
					'price' => 3.50,
				),
				'print' => array(
					'label' => 'Bear',
					'value' => 'bear',
				),
			),
		));

		$item = $this->cart->items()->first();

		$this->assertEquals($item->attributes()->count(), 3);

		$this->assertEquals($this->cart->items()->count(), 1);

		$this->assertEquals($this->cart->quantity(), 2);

		$this->assertEquals($this->cart->total(), 267);
	}


	public function testAddMultipleItemsWithAttributes()
	{
		$this->cart->add(array(
			array(
				'id'         => 'foobar1',
				'name'       => 'Foobar 1',
				'quantity'   => '03',
				'price'      => 4,
				'attributes' => array(
					'size'  => array(
						'label' => 'Large',
						'value' => 'l',
						'price' => 5.00,
					),
					'color' => array(
						'label' => 'Red',
						'value' => 'red',
						'price' => 3.50,
					),
				),
			),
			array(
				'id'       => 'foobar2',
				'name'     => 'Foobar 2',
				'quantity' => 2,
				'price'    => 21.00,
			),
			array(
				'id'         => 'foobar3',
				'name'       => 'Foobar 3',
				'quantity'   => 2,
				'price'      => 120.00,
				'attributes' => array(
					'color' => array(
						'label' => 'Blue',
						'value' => 'blue',
						'price' => 3.50,
					),
				),
			),
		));

		$firstItem = $this->cart->items()->first();

		$lastItem = $this->cart->items()->last();

		$this->assertEquals($firstItem->attributes()->count(), 2);

		$this->assertEquals($lastItem->attributes()->count(), 1);

		$this->assertEquals($this->cart->items()->count(), 3);

		$this->assertEquals($this->cart->quantity(), 7);

		$this->assertEquals($this->cart->total(), 326.50);
	}

}
