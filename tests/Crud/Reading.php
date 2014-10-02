<?php namespace Cartalyst\Cart\Tests\Crud;
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
 * @version    1.0.4
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Cart\Tests\CartTestCase;

class Reading extends CartTestCase {

	/** @test */
	public function it_can_get_an_item_information()
	{
		$item1 = $this->createItem('Foobar 1', 97, 4);
		$item2 = $this->createItem('Foobar 2', 21, 2);

		$this->cart->add([$item1, $item2]);

		$this->assertEquals($this->cart->item('b37f673e46a33038305c1dc411215c07')->get('price'), 97);
	}

    /** @test */
    public function it_can_get_an_item_subprice()
    {
        $item = $this->createItem('Foobar 1', 100, 1,[],[10,20]);
        $item = $this->cart->add($item);

        $this->assertEquals($item->subprice(), 130);
    }

	/** @test */
	public function it_can_get_an_item_subtotal()
	{
		$item = $this->createItem('Foobar 1', 97, 4);
		$item = $this->cart->add($item);

		$this->assertEquals($item->subtotal(), 388);
	}

	/** @test */
	public function it_can_get_an_item_total()
	{
		$item = $this->createItem('Foobar 1', 97, 5);
		$item = $this->cart->add($item);

		$this->assertEquals($item->total(), 485);
	}

	/** @test */
	public function it_can_get_an_item_quantity()
	{
		$item = $this->createItem('Foobar 1', 97, 4);
		$item = $this->cart->add($item);

		$this->assertEquals($item->quantity(), 4);
	}

	/** @test */
	public function it_can_get_an_item_weight()
	{
		$item = $this->createItem('Foobar 1', 97, 4, null, null, 21.00);
		$item = $this->cart->add($item);

		$this->assertEquals($item->weight(), 84);
	}

}
