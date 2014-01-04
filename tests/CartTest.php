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


	public function testcartCanBeInstantiated()
	{
		$storage = m::mock('Cartalyst\Cart\Storage\Sessions\IlluminateSession');

		$dispatcher = m::mock('Illuminate\Events\Dispatcher');

		new Cart($storage, $dispatcher);
	}


	public function testCanAdd()
	{
		$cart = m::mock('cart');
		$cart->shouldReceive('add')->once();

		$cart->add();
	}


	public function testCanUpdate()
	{
		$cart = m::mock('cart');
		$cart->shouldReceive('update')->once();

		$cart->update();
	}


	public function testCanDelete()
	{
		$cart = m::mock('cart');
		$cart->shouldReceive('delete')->once();

		$cart->delete();
	}


	public function testSetRequiredIndexes()
	{
		$indexes = array(
			'price',
		);

		$this->cart->setRequiredIndexes($indexes);

		$this->assertTrue(in_array('price', $this->cart->getRequiredIndexes()));
	}


	public function testAddSingleItemToCart()
	{
		$this->cart->add(
			array(
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => 2,
				'price'    => 125.00,
			)
		);

		$this->assertEquals($this->cart->items()->count(), 1);

		$this->assertEquals($this->cart->quantity(), 2);

		$this->assertEquals($this->cart->total(), 250);
	}


	public function testAddSingleItemWithAttributesToCart()
	{
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
						'label' => 'Red',
						'value' => 'red',
						'price' => 3.50,
					),
					'print' => array(
						'label' => 'Bear',
						'value' => 'bear',
					),
				),
			)
		);

		$this->assertEquals($this->cart->items()->count(), 1);

		$this->assertEquals($this->cart->quantity(), 2);

		$this->assertEquals($this->cart->total(), 267);
	}


	public function testAddMultipleItemsToCart()
	{
		$this->cart->add(
			array(
				array(
					'id'       => 'foobar1',
					'name'     => 'Foobar 1',
					'quantity' => '03',
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
			)
		);

		$this->assertEquals($this->cart->items()->count(), 3);

		$this->assertEquals($this->cart->quantity(), 7);

		$this->assertEquals($this->cart->total(), 294);
	}


	public function testAddMultipleItemsWithAttributesToCart()
	{
		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => '03',
					'price'      => 4,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
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
			)
		);

		$this->assertEquals($this->cart->items()->count(), 3);

		$this->assertEquals($this->cart->quantity(), 7);

		$this->assertEquals($this->cart->total(), 326.50);
	}


	public function testReAddExistingItemToUpdateTheQuantity()
	{
		$this->cart->add(
			array(
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => 3,
				'price'    => 4,
			)
		);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->get('quantity'), 3);

		$this->cart->add(
			array(
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => 6,
				'price'    => 4,
			)
		);

		$this->assertEquals($item->get('quantity'), 9);
	}


	public function testUpdateItemQuantity()
	{
		$this->cart->add(
			array(
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => 3,
				'price'    => 4,
			)
		);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->get('quantity'), 3);

		$this->cart->update('f53e8bcc3534788e4b4f296c1889cc99', 2);

		$this->assertEquals($item->get('quantity'), 2);
	}


	public function testUpdateItemProperties()
	{
		$this->cart->add(
			array(
				array(
					'id'         => 'foobar2',
					'name'       => 'Foobar 2',
					'quantity'   => 3,
					'price'      => 120.00,
					'attributes' => array(
						'size' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 15.00,
						),
					),
				),
			)
		);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->get('quantity'), 3);

		$this->assertEquals($item->get('name'), 'Foobar 2');

		$this->assertEquals($item->get('attributes')->first()->get('value'), 'L');

		$this->cart->update('27af518595dfd39ae436c70af8a74561', array(
			'name'       => 'Foo',
			'quantity'   => 6,
			'attributes' => array(
				'size' => array(
					'label' => 'Size',
					'value' => 'M',
					'price' => 15.00,
				),
			),
		));

		$this->assertEquals($item->get('quantity'), 6);

		$this->assertEquals($item->get('name'), 'Foo');

		$this->assertEquals($item->get('attributes')->first()->get('value'), 'M');
	}


	public function testUpdateMultipleItemsQuantityAndPrice()
	{
		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 7,
					'price'      => 120.00,
					'attributes' => array(
						'size' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 15.00,
						),
					),
				),
				array(
					'id'         => 'foobar2',
					'name'       => 'Foobar 2',
					'quantity'   => 3,
					'price'      => 120.00,
					'attributes' => array(
						'size' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 15.00,
						),
					),
				),
			)
		);

		$this->cart->update(array(
			'2c64e56be9013bed1a17e9156e53609b' => array('price' => 20.00, 'quantity' => 3),
			'27af518595dfd39ae436c70af8a74561' => array('price' => 25.00, 'quantity' => 2),
		));

		$this->assertEquals($this->cart->items()->first()->get('quantity'), 3);
		$this->assertEquals($this->cart->items()->last()->get('quantity'), 2);

		$this->assertEquals($this->cart->items()->first()->get('price'), 20.00);
		$this->assertEquals($this->cart->items()->last()->get('price'), 25.00);
	}


	public function testDeleteSingleItem()
	{
		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 7,
					'price'      => 120.00,
					'attributes' => array(
						'size' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 15.00,
						),
					),
				),
				array(
					'id'         => 'foobar2',
					'name'       => 'Foobar 2',
					'quantity'   => 3,
					'price'      => 120.00,
					'attributes' => array(
						'size' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 15.00,
						),
					),
				),
			)
		);

		$this->assertEquals($this->cart->items()->count(), 2);

		$this->cart->remove('2c64e56be9013bed1a17e9156e53609b');

		$this->assertEquals($this->cart->items()->count(), 1);

		$this->assertEmpty($this->cart->find(array('2c64e56be9013bed1a17e9156e53609b')));
	}


	public function testDeleteMultipleItemsInline()
	{
		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 7,
					'price'      => 120.00,
					'attributes' => array(
						'size' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 15.00,
						),
					),
				),
				array(
					'id'         => 'foobar2',
					'name'       => 'Foobar 2',
					'quantity'   => 3,
					'price'      => 120.00,
					'attributes' => array(
						'size' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 15.00,
						),
					),
				),
			)
		);

		$this->assertEquals($this->cart->items()->count(), 2);

		$this->cart->remove('2c64e56be9013bed1a17e9156e53609b', '27af518595dfd39ae436c70af8a74561');

		$this->assertEquals($this->cart->items()->count(), 0);
	}


	public function testDeleteMultipleItemsArray()
	{
		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 7,
					'price'      => 120.00,
					'attributes' => array(
						'size' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 15.00,
						),
					),
				),
				array(
					'id'         => 'foobar2',
					'name'       => 'Foobar 2',
					'quantity'   => 3,
					'price'      => 120.00,
					'attributes' => array(
						'size' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 15.00,
						),
					),
				),
			)
		);

		$this->assertEquals($this->cart->items()->count(), 2);

		$this->cart->remove(array('2c64e56be9013bed1a17e9156e53609b', '27af518595dfd39ae436c70af8a74561'));

		$this->assertEquals($this->cart->items()->count(), 0);
	}


	public function testTotalNumberOfItemsInCart()
	{
		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 4,
					'price'      => 97.00,
					'weight'	 => 21.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
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
							'label' => 'Size',
							'value' => 'L',
							'price' => 15.00,
						),
					),
				),
			)
		);

		$this->assertEquals($this->cart->quantity(), 6);
	}


	public function testCartWeight()
	{
		$this->cart->add(
			array(
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
			)
		);

		$this->assertEquals($this->cart->weight(), 126);
	}


	public function testItemWeight()
	{
		$this->cart->add(
			array(
				array(
					'id'       => 'foobar1',
					'name'     => 'Foobar 1',
					'quantity' => 4,
					'price'    => 97.00,
					'weight'=> 21.00,
				),
			)
		);

		$this->assertEquals($this->cart->items()->first()->weight(), 84);
	}


	public function testClearCart()
	{
		$this->cart->add(
			array(
				array(
					'id'       => 'foobar1',
					'name'     => 'Foobar 1',
					'quantity' => 4,
					'price'    => 97.00,
				),
			)
		);

		$this->assertEquals($this->cart->quantity(), 4);

		$this->cart->clear();

		$this->assertEmpty($this->cart->items()->toArray());
	}


	public function testDestroyCart()
	{
		$this->cart->add(
			array(
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
				),
			)
		);

		$this->assertEquals($this->cart->items()->count(), 1);

		$this->cart->destroy();

		$this->assertEquals($this->cart->items()->count(), 0);
	}


	public function testFindItemsByProperties()
	{
		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 2,
					'price'      => 97.00,
					'weight'	 => 21.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'S',
						),
						'color' => array(
							'label' => 'Color',
							'value' => 'Red',
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
							'label' => 'Size',
							'value' => 'L',
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
							'label' => 'Size',
							'value' => 'L',
							'price' => 5.00,
						),
					),
				),
			)
		);

		$item = $this->cart->find(
			array(
				'price' => 85,
			)
		);

		$this->assertEquals($item[0]->get('id'), 'foobar2');
	}


	public function testFindItemsByAttributes()
	{
		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 2,
					'price'      => 97.00,
					'weight'	 => 21.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'S',
						),
						'color' => array(
							'label' => 'Color',
							'value' => 'Red',
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
							'label' => 'Size',
							'value' => 'L',
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
							'label' => 'Size',
							'value' => 'L',
							'price' => 5.00,
						),
					),
				),
			)
		);

		$item = $this->cart->find(
			array(
				'attributes' => array(
					'size' => array(
						'value' => 'L',
					)
				)
			)
		);

		$this->assertEquals($item[0]->get('id'), 'foobar2');
		$this->assertEquals($item[1]->get('id'), 'foobar3');
	}

}
