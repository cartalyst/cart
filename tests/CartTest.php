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

	/**
	 * @test
	 */
	public function cart_can_be_instantiated()
	{
		$storage = m::mock('Cartalyst\Cart\Storage\Sessions\IlluminateSession');

		$dispatcher = m::mock('Illuminate\Events\Dispatcher');

		new Cart('cart', $storage, $dispatcher);
	}

	/**
	 * @test
	 */
	public function it_can_set_the_required_indexes()
	{
		$indexes = array(
			'price',
		);

		$this->cart->setRequiredIndexes($indexes);

		$this->assertTrue(in_array('price', $this->cart->getRequiredIndexes()));
	}

	/**
	 * @test
	 */
	public function it_can_get_the_cart_identity()
	{
		$this->assertEquals($this->cart->getIdentity(), 'cart');
	}

	/**
	 * @test
	 */
	public function it_can_set_the_cart_identity()
	{
		$this->cart->setIdentity('testCart');

		$this->assertEquals($this->cart->getIdentity(), 'testCart');
	}

	/**
	 * @test
	 */
	public function it_can_get_the_cart_storage()
	{
		$this->assertTrue($this->cart->getStorage() instanceof \Cartalyst\Cart\Storage\StorageInterface);
	}

	/**
	 * @test
	 */
	public function it_can_set_the_cart_storage()
	{
		$storage = m::mock('\Cartalyst\Cart\Storage\StorageInterface');

		$this->cart->setStorage($storage);

		$this->assertTrue($this->cart->getStorage() instanceof \Cartalyst\Cart\Storage\StorageInterface);
	}

	/**
	 * @test
	 */
	public function it_can_get_the_total_number_of_items_inside_the_cart()
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

	/**
	 * @test
	 */
	public function it_can_get_the_total_cart_weight()
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

	/**
	 * @test
	 */
	public function cart_can_be_cleared()
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

	/**
	 * @test
	 */
	public function cart_can_be_searched()
	{
		$this->cart->add(array(
			array(
				'id'         => 'foobar1',
				'name'       => 'Foobar 1',
				'quantity'   => 2,
				'price'      => 97.00,
			),
			array(
				'id'         => 'foobar2',
				'name'       => 'Foobar 2',
				'quantity'   => 2,
				'price'      => 85.00,
			),
			array(
				'id'         => 'foobar3',
				'name'       => 'Foobar 3',
				'quantity'   => 5,
				'price'      => 35.00,
			),
		));

		$items = $this->cart->find(array(
			'price'    => 85,
			'quantity' => 2,
		));

		$this->assertEquals($items[0]->get('id'), 'foobar2');

		$this->assertEquals(count($items), 1);
	}

	/**
	 * @test
	 */
	public function cart_can_be_searched_by_items_attributes()
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
			'price'    => 85,
			'quantity' => 2,
			'weight'   => 21,
			'attributes' => array(
				'size' => array(
					'label' => 'Large',
					'price' => 15,
				),
			),
		));

		$this->assertEquals($item[0]->get('id'), 'foobar2');

		$items = $this->cart->find(array(
			'attributes' => array(
				'size' => array(
					'value' => 'l',
				),
			),
		));

		$this->assertEquals(count($items), 2);

		$this->assertEquals($items[0]->get('id'), 'foobar2');

		$this->assertEquals($items[1]->get('id'), 'foobar3');
	}

	/**
	 * @test
	 */
 	public function cart_can_be_searched_and_returning_empty_results()
	{
		$this->cart->add(array(
			array(
				'id'         => 'foobar2',
				'name'       => 'Foobar 2',
				'quantity'   => 2,
				'price'      => 200.00,
			),
		));

		$item = $this->cart->find(array(
			'price' => 85,
			'attributes' => array(
				'color' => array(
					'label' => 'red',
				),
			),
		));

		$this->assertEquals(count($item), 0);
	}

}
