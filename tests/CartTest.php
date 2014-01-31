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

class CartTest extends PHPUnit_Framework_TestCase {

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


	public function testcartCanBeInstantiated()
	{
		$storage = m::mock('Cartalyst\Cart\Storage\Sessions\IlluminateSession');

		$dispatcher = m::mock('Illuminate\Events\Dispatcher');

		new Cart('cart', $storage, $dispatcher);
	}


	public function testSetRequiredIndexes()
	{
		$indexes = array(
			'price',
		);

		$this->cart->setRequiredIndexes($indexes);

		$this->assertTrue(in_array('price', $this->cart->getRequiredIndexes()));
	}


	public function testTotalNumberOfItemsInCart()
	{
		$this->cart->add(array(
			array(
				'id'         => 'foobar1',
				'name'       => 'Foobar 1',
				'quantity'   => 4,
				'price'      => 97.00,
				'weight'	 => 21.00,
				'attributes' => array(
					'size'  => array(
						'label' => 'Large',
						'value' => 'l',
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
				'weight'	 => 21.00,
				'attributes' => array(
					'size' => array(
						'label' => 'Large',
						'value' => 'l',
						'price' => 15.00,
					),
				),
			),
		));

		$this->assertEquals($this->cart->quantity(), 6);
	}


	public function testCartWeight()
	{
		$this->cart->add(array(
			array(
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => 4,
				'price'    => 97.00,
				'weight'   => 21.00,
			),
			array(
				'id'       => 'foobar2',
				'name'     => 'Foobar 2',
				'quantity' => 2,
				'price'    => 85.00,
				'weight'   => 21.00,
			),
		));

		$this->assertEquals($this->cart->weight(), 126);
	}


	public function testClearCart()
	{
		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 4,
			'price'    => 97.00,
		));

		$this->assertEquals($this->cart->quantity(), 4);

		$this->cart->clear();

		$this->assertEmpty($this->cart->items()->toArray());
	}


	public function testSearchWithoutReturningAnyResults()
	{
		$item = $this->cart->find(array(
			'price' => 85,
		));

		$this->assertEquals(count($item), 0);
	}


	public function testFindItemsByProperties()
	{
		$this->cart->add(array(
			array(
				'id'         => 'foobar1',
				'name'       => 'Foobar 1',
				'quantity'   => 2,
				'price'      => 97.00,
				'weight'	 => 21.00,
				'attributes' => array(
					'size'  => array(
						'label' => 'Size',
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
				'weight'	 => 21.00,
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
				'weight'	 => 21.00,
				'attributes' => array(
					'size' => array(
						'label' => 'Large',
						'value' => 'l',
						'price' => 5.00,
					),
				),
			),
		));

		$item = $this->cart->find(array(
			'price' => 85,
		));

		$this->assertEquals($item[0]->get('id'), 'foobar2');

		$items = $this->cart->find(array(
			'attributes' => array(
				'size' => array(
					'value' => 'l'
				),
			),
		));

		$this->assertEquals(count($items), 2);

		$this->assertEquals($items[0]->get('id'), 'foobar2');

		$this->assertEquals($items[1]->get('id'), 'foobar3');
	}


	public function testFindItemsByAttributes()
	{
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
				'weight'	 => 21.00,
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
				'price'      => 85.00,
				'weight'	 => 21.00,
				'attributes' => array(
					'size' => array(
						'label' => 'Large',
						'value' => 'l',
						'price' => 5.00,
					),
				),
			),
		));

		$item = $this->cart->find(array(
			'attributes' => array(
				'size' => array(
					'value' => 'l',
				),
			),
		));

		$this->assertEquals($item[0]->get('id'), 'foobar2');

		$this->assertEquals($item[1]->get('id'), 'foobar3');
	}

}
