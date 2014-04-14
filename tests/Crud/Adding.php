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

use Cartalyst\Cart\Tests\CartTestCase;
use Illuminate\Support\Collection;

class Adding extends CartTestCase {

	/** @test */
	public function it_can_add_a_single_item()
	{
		$item = $this->createItem('Foobar 1', 10.00, 2);

		$this->cart->add($item);

		$this->assertEquals($this->cart->quantity(), 2);

		$this->assertEquals($this->cart->items()->count(), 1);
	}

	/** @test */
	public function it_can_add_a_single_item_with_quantity_as_string()
	{
		$item = $this->createItem('Foobar 1', 10.00, '0000002');

		$this->cart->add($item);

		$this->assertEquals($this->cart->quantity(), 2);
	}

	/** @test */
	public function it_can_add_a_single_item_with_price_as_string()
	{
		$item = $this->createItem('Foobar 1', '10.00', 2);

		$this->cart->add($item);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->get('price'), 10);
	}

	/** @test */
	public function it_can_add_a_single_item_with_attributes()
	{
		$item = $this->createItem('Foobar 1', 125, 2, null, [5, 3.5]);

		$this->cart->add($item);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->attributes()->count(), 2);

		$this->assertEquals($this->cart->items()->count(), 1);
		$this->assertEquals($this->cart->quantity(), 2);
		$this->assertEquals($this->cart->total(), 267);
	}

	/** @test */
	public function it_can_add_multiple_items()
	{
		$item1 = $this->createItem('Foobar 1', 4, 3);
		$item2 = $this->createItem('Foobar 2', 21, 2);
		$item3 = $this->createItem('Foobar 3', 120, 2);

		$this->cart->add([$item1, $item2, $item3]);

		$this->assertEquals($this->cart->items()->count(), 3);
		$this->assertEquals($this->cart->quantity(), 7);
		$this->assertEquals($this->cart->total(), 294);
	}

	/** @test */
	public function it_can_add_existing_item_to_update_its_quantity()
	{
		$item = $this->createItem('Foobar 1', 4, 3);

		$this->cart->add($item);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->get('quantity'), 3);

		$item = $this->createItem('Foobar 1', 4, 6);

		$this->cart->add($item);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->get('quantity'), 9);
	}

	/** @test */
	public function it_can_add_multiple_items_with_attributes()
	{
		$item1 = $this->createItem('Foobar 1', 4, 03, null, [5, 3.50]);
		$item2 = [
			'id'         => 'foobar3',
			'name'       => 'Foobar 3',
			'quantity'   => 4,
			'price'      => 120.00,
			'attributes' => [
				'color' => [
					'label' => 'Blue',
					'value' => 'blue',
					'price' => 3.50,
				],
			],
		];

		$this->cart->add([$item1, $item2]);

		$firstItem = $this->cart->items()->first();
		$lastItem  = $this->cart->items()->last();

		$this->assertEquals($firstItem->attributes()->count(), 2);
		$this->assertEquals($lastItem->attributes()->count(), 1);

		$this->assertEquals($this->cart->items()->count(), 2);
		$this->assertEquals($this->cart->quantity(), 7);
		$this->assertEquals($this->cart->total(), 531.5);
	}

	/** @test */
	public function it_can_sync_data_from_a_collection()
	{
		$item1 = $this->createItem('Foobar 1', 50, 1);
		$item2 = $this->createItem('Foobar 2', 50, 1);

		$this->assertEquals($this->cart->items()->count(), 0);

		$data = new Collection([$item1, $item2]);

		$this->cart->sync($data);

		$this->assertEquals($this->cart->items()->count(), 2);
	}

}
