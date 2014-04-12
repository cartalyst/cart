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
		$this->cart->add([
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 2,
			'price'    => 10.00,
		]);

		$this->assertEquals($this->cart->quantity(), 2);

		$this->assertEquals($this->cart->items()->count(), 1);
	}

	/** @test */
	public function it_can_add_a_single_item_with_quantity_as_string()
	{
		$this->cart->add([
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => '0000002',
			'price'    => 10.00,
		]);

		$this->assertEquals($this->cart->quantity(), 2);
	}

	/** @test */
	public function it_can_add_a_single_item_with_price_as_string()
	{
		$this->cart->add([
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 2,
			'price'    => '10.00',
		]);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->get('price'), 10);
	}

	/** @test */
	public function it_can_add_a_single_item_with_attributes()
	{
		$this->cart->add([
			'id'         => 'foobar1',
			'name'       => 'Foobar 1',
			'quantity'   => 2,
			'price'      => 125.00,
			'attributes' => [
				'size'  => [
					'label' => 'Large',
					'value' => 'l',
					'price' => 5.00,
				],
				'color' => [
					'label' => 'Red',
					'value' => 'red',
					'price' => 3.50,
				],
				'print' => [
					'label' => 'Bear',
					'value' => 'bear',
				],
			],
		]);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->attributes()->count(), 3);

		$this->assertEquals($this->cart->items()->count(), 1);
		$this->assertEquals($this->cart->quantity(), 2);
		$this->assertEquals($this->cart->total(), 267);
	}

	/** @test */
	public function it_can_add_multiple_items()
	{
		$this->cart->add([
			[
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => 3,
				'price'    => 4,
			],
			[
				'id'       => 'foobar2',
				'name'     => 'Foobar 2',
				'quantity' => 2,
				'price'    => 21.00,
			],
			[
				'id'       => 'foobar3',
				'name'     => 'Foobar 3',
				'quantity' => 2,
				'price'    => 120.00,
			],
		]);

		$this->assertEquals($this->cart->items()->count(), 3);
		$this->assertEquals($this->cart->quantity(), 7);
		$this->assertEquals($this->cart->total(), 294);
	}

	/** @test */
	public function it_can_add_existing_item_to_update_its_quantity()
	{
		$this->cart->add([
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 3,
			'price'    => 4,
		]);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->get('quantity'), 3);

		$this->cart->add([
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 6,
			'price'    => 4,
		]);

		$this->assertEquals($item->get('quantity'), 9);
	}

	/** @test */
	public function it_can_add_multiple_items_with_attributes()
	{
		$this->cart->add([
			[
				'id'         => 'foobar1',
				'name'       => 'Foobar 1',
				'quantity'   => '03',
				'price'      => 4,
				'attributes' => [
					'size'  => [
						'label' => 'Large',
						'value' => 'l',
						'price' => 5.00,
					],
					'color' => [
						'label' => 'Red',
						'value' => 'red',
						'price' => 3.50,
					],
				],
			],
			[
				'id'       => 'foobar2',
				'name'     => 'Foobar 2',
				'quantity' => 2,
				'price'    => 21.00,
			],
			[
				'id'         => 'foobar3',
				'name'       => 'Foobar 3',
				'quantity'   => 2,
				'price'      => 120.00,
				'attributes' => [
					'color' => [
						'label' => 'Blue',
						'value' => 'blue',
						'price' => 3.50,
					],
				],
			],
		]);

		$firstItem = $this->cart->items()->first();
		$lastItem  = $this->cart->items()->last();

		$this->assertEquals($firstItem->attributes()->count(), 2);
		$this->assertEquals($lastItem->attributes()->count(), 1);

		$this->assertEquals($this->cart->items()->count(), 3);
		$this->assertEquals($this->cart->quantity(), 7);
		$this->assertEquals($this->cart->total(), 326.50);
	}

	/** @test */
	public function it_can_sync_data_from_a_collection()
	{
		$this->assertEquals($this->cart->items()->count(), 0);

		$data = new Collection([
			[
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'price'    => 50,
				'quantity' => 1,
			],
			[
				'id'       => 'foobar2',
				'name'     => 'Foobar 2',
				'price'    => 50,
				'quantity' => 1,
			],
		]);

		$this->cart->sync($data);

		$this->assertEquals($this->cart->items()->count(), 2);
	}

}
