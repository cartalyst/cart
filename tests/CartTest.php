<?php namespace Cartalyst\Cart\Tests;
/**
 * Part of the Cart package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Cart
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

require 'CartTestCase.php';

use Cartalyst\Cart\Cart;
use Cartalyst\Cart\Storage\IlluminateSession;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\FileSessionHandler;
use Illuminate\Session\Store;
use Mockery as m;

class CartTest extends CartTestCase {

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
	public function cart_can_be_instantiated()
	{
		$storage = m::mock('Cartalyst\Cart\Storage\IlluminateSession');

		$dispatcher = m::mock('Illuminate\Events\Dispatcher');

		new Cart('cart', $storage, $dispatcher);
	}

	/** @test */
	public function it_can_set_the_required_indexes()
	{
		$indexes = [
			'price',
		];

		$this->cart->setRequiredIndexes($indexes);

		$this->assertTrue(in_array('price', $this->cart->getRequiredIndexes()));
	}

	/** @test */
	public function it_can_get_the_cart_identity()
	{
		$this->assertEquals($this->cart->getIdentity(), 'cart');
	}

	/** @test */
	public function it_can_set_the_cart_identity()
	{
		$this->cart->setIdentity('testCart');

		$this->assertEquals($this->cart->getIdentity(), 'testCart');
	}

	/** @test */
	public function it_can_get_the_cart_storage()
	{
		$this->assertTrue($this->cart->getStorage() instanceof \Cartalyst\Cart\Storage\StorageInterface);
	}

	/** @test */
	public function it_can_set_the_cart_storage()
	{
		$storage = m::mock('\Cartalyst\Cart\Storage\StorageInterface');

		$this->cart->setStorage($storage);

		$this->assertTrue($this->cart->getStorage() instanceof \Cartalyst\Cart\Storage\StorageInterface);
	}

	/** @test */
	public function it_can_get_the_cart_dispatcher()
	{
		$this->assertTrue($this->cart->getDispatcher() instanceof \Illuminate\Events\Dispatcher);
	}

	/** @test */
	public function it_can_set_the_cart_dispatcher()
	{
		$dispatcher = m::mock('\Illuminate\Events\Dispatcher');

		$this->cart->setDispatcher($dispatcher);

		$this->assertTrue($this->cart->getDispatcher() instanceof \Illuminate\Events\Dispatcher);
	}

	/** @test */
	public function it_can_get_the_total_number_of_items_inside_the_cart()
	{
		$item1 = $this->createItem('Foobar 1', 97, 4);
		$item2 = $this->createItem('Foobar 2', 85, 2);

		$this->cart->add([$item1, $item2]);

		$this->assertEquals($this->cart->quantity(), 6);
	}

	/** @test */
	public function it_can_get_the_total_cart_weight()
	{
		$item1 = $this->createItem('Foobar 1', 97, 4, null, null, 21.49);
		$item2 = $this->createItem('Foobar 2', 85, 2, null, null, 21.32);
		$item3 = [
			'id'         => 'foobar2',
			'name'       => 'Foobar 2',
			'quantity'   => 2,
			'price'      => 200.00,
			'weight'     => 20.00,
			'attributes' => [
				'size' => [
					'label' => 'Large',
					'value' => 'l',
					'weight' => 1.5,
				],
				'shape' => [
					'label' => 'Circle',
					'value' => 'c',
					'weight' => 10.00,
				],
			],
		];

		$this->cart->add([$item1, $item2, $item3]);

		$this->assertEquals($this->cart->weight(), 191.6);
	}

	/** @test */
	public function cart_can_be_cleared()
	{
		$item = $this->createItem('Foobar 1', 97, 4, null, null, 21.49);

		$this->cart->add($item);

		$this->assertEquals($this->cart->quantity(), 4);

		$this->cart->clear();

		$this->assertEmpty($this->cart->items()->toArray());
	}

	/** @test */
	public function cart_can_be_searched()
	{
		$item1 = $this->createItem('Foobar 1', 97, 2, null, [0, 17.00], 21.00);
		$item2 = $this->createItem('Foobar 2', 85, 2, null, [15, 0], 21.00);
		$item3 = $this->createItem('Foobar 3', 35, 5, null, [5, 17.00], 21.00);

		$this->cart->add([$item1, $item2, $item3]);

		$items = $this->cart->find([
			'price'    => 85,
			'quantity' => 2,
		]);

		$this->assertEquals($items[0]->get('id'), 'foobar2');

		$this->assertCount(1, $items);
	}

	/** @test */
	public function cart_can_be_searched_by_items_attributes()
	{
		$item1 = $this->createItem('Foobar 1', 97, 2, null, [0, 17.00], 21.00);
		$item2 = $this->createItem('Foobar 2', 85, 2, null, [15, 0], 21.00);
		$item3 = $this->createItem('Foobar 3', 35, 5, null, [5, 17.00], 21.00);

		$this->cart->add([$item1, $item2, $item3]);

		$item = $this->cart->find([
			'price'    => 85,
			'quantity' => 2,
			'weight'   => 21,
		]);

		$this->assertEquals($item[0]->get('id'), 'foobar2');
		$this->assertEquals($item[0]->price(), 85);

		$items = $this->cart->find([
			'attributes' => [
				'color' => [
					'price' => 17,
				],
			],
		]);

		$this->assertCount(2, $items);
		$this->assertEquals($items[0]->get('id'), 'foobar1');
		$this->assertEquals($items[1]->get('id'), 'foobar3');
	}

	/** @test */
 	public function cart_can_be_searched_and_returning_empty_results()
	{
		$item = [
			'id'       => 'foobar2',
			'name'     => 'Foobar 2',
			'quantity' => 2,
			'price'    => 200.00,
		];

		$this->cart->add($item);

		$item = $this->cart->find([
			'price' => 85,
			'attributes' => [
				'color' => [
					'label' => 'Red',
				],
			],
		]);

		$this->assertEmpty($item);
	}

	/** @test */
	public function see_if_item_exists()
	{
		$item = $this->createItem('Foobar 1', 200, 2);

		$this->cart->add($item);

		$this->assertEquals(true, $this->cart->exists('b37f673e46a33038305c1dc411215c07'));
	}

	/** @test */
	public function see_if_item_does_not_exist()
	{
		$this->assertEquals(false, $this->cart->exists('foobar'));
	}

	/** @test */
	public function it_can_set_meta_data()
	{
		$this->cart->setMetaData(['abc' => 'aaa']);
	}

	/** @test */
	public function it_can_retrieve_meta_data()
	{
		$this->cart->setMetaData([
			'shipping_info' => [
				'personal_details' => [
					'name' => 'John Doe',
				],
				'billing_address' => [
					'house'  => 123,
					'street' => '123 Street.',
				],
			]
		]);

		$this->assertEquals($this->cart->getMetaData('shipping_info.personal_details.name'), 'John Doe');
	}

	/** @test */
	public function it_can_remove_meta_data()
	{
		$this->cart->setMetaData([
			'shipping_info' => [
				'personal_details' => [
					'name' => 'John Doe',
				],
				'billing_address' => [
					'house'  => 123,
					'street' => '123 Street.',
				],
			]
		]);

		$this->assertEquals($this->cart->getMetaData('shipping_info.personal_details.name'), 'John Doe');

		$this->cart->removeMetaData('shipping_info.personal_details');

		$this->assertEmpty($this->cart->getMetaData('shipping_info.personal_details'));
		$this->assertEquals($this->cart->getMetaData('shipping_info.billing_address.house'), 123);

		$this->cart->removeMetaData();

		$this->assertEmpty($this->cart->getMetaData('shipping_info.personal_details'));
		$this->assertEmpty($this->cart->getMetaData('shipping_info.billing_address'));
	}

}
