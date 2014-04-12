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

class Removing extends CartTestCase {

	/** @test */
	public function it_can_remove_a_single_item()
	{
		$this->cart->add([
			[
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => 7,
				'price'    => 120.00,
			],
			[
				'id'       => 'foobar2',
				'name'     => 'Foobar 2',
				'quantity' => 3,
				'price'    => 120.00,
			],
		]);

		$this->assertEquals($this->cart->quantity(), 10);
		$this->assertEquals($this->cart->items()->count(), 2);

		$this->cart->remove('f53e8bcc3534788e4b4f296c1889cc99');

		$this->assertEquals($this->cart->quantity(), 3);
		$this->assertEquals($this->cart->items()->count(), 1);
		$this->assertEmpty($this->cart->find(['f53e8bcc3534788e4b4f296c1889cc99']));
	}

	/** @test */
	public function it_can_remove_multiple_items()
	{
		$this->cart->add([
			[
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => 7,
				'price'    => 120.00,
			],
			[
				'id'       => 'foobar2',
				'name'     => 'Foobar 2',
				'quantity' => 3,
				'price'    => 120.00,
			],
		]);

		$this->assertEquals($this->cart->items()->count(), 2);

		$this->cart->remove(['f53e8bcc3534788e4b4f296c1889cc99', '2d2d8cb241842b326ce0e095dbfc4d41']);

		$this->assertEquals($this->cart->items()->count(), 0);
	}

}
