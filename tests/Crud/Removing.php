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
		$item1 = $this->createItem('Foobar 1', 120, 7);
		$item2 = $this->createItem('Foobar 2', 120, 3);

		$this->cart->add([$item1, $item2]);

		$this->assertEquals($this->cart->quantity(), 10);
		$this->assertEquals($this->cart->items()->count(), 2);

		$this->cart->remove('b37f673e46a33038305c1dc411215c07');

		$this->assertEquals($this->cart->quantity(), 3);
		$this->assertEquals($this->cart->items()->count(), 1);
		$this->assertEmpty($this->cart->find(['b37f673e46a33038305c1dc411215c07']));
	}

	/** @test */
	public function it_can_remove_multiple_items()
	{
		$item1 = $this->createItem('Foobar 1', 120, 7);
		$item2 = $this->createItem('Foobar 2', 120, 3);

		$this->cart->add([$item1, $item2]);

		$this->cart->remove(['b37f673e46a33038305c1dc411215c07', '07d732dbcc3ce0752ac74870d6fa2194']);

		$this->assertEquals($this->cart->items()->count(), 0);
	}

}
