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
 * @version    1.0.5
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

class CartTestExceptions extends CartTestCase {

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException
	 */
	public function it_throws_exception_when_missing_a_required_index()
	{
		$this->cart->add([
			'name'     => 'foo',
			'price'    => 20.00,
			'quantity' => 5,
		]);
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException
	 */
	public function it_throws_exception_when_missing_a_required_index_on_attributes()
	{
		$this->cart->add([
			'id'         => 'foo',
			'name'       => 'bar',
			'price'      => 20.00,
			'quantity'   => 5,
			'attributes' => [
				'print' => [
					'label' => 'Bear',
				],
			],
		]);
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartInvalidQuantityException
	 */
	public function it_throws_exception_when_adding_single_item_with_invalid_quantity()
	{
		$this->cart->add([
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => -2,
			'price'    => 125.00,
		]);
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartInvalidPriceException
	 */
	public function it_throws_exception_when_adding_single_item_with_invalid_price()
	{
		$this->cart->add([
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 1,
			'price'    => 'foo',
		]);
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartInvalidQuantityException
	 */
	public function it_throws_exception_when_invalid_quantity_is_passed()
	{
		$this->cart->add([
			'id'       => 1,
			'name'     => 'foo',
			'price'    => 20.00,
			'quantity' => 'bar',
		]);
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartInvalidPriceException
	 */
	public function it_throws_exception_when_invalid_price_is_passed()
	{
		$this->cart->add([
			'id'       => 1,
			'name'     => 'foo',
			'price'    => 'bar',
			'quantity' => 5,
		]);
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartInvalidAttributesException
	 */
	public function it_throws_exception_when_invalid_attributes_are_passed()
	{
		$this->cart->add([
			'id'         => 1,
			'name'       => 'foo',
			'price'      => 20.00,
			'quantity'   => 5,
			'attributes' => 'bar',
		]);
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function it_throws_exception_updating_an_item_that_does_not_exist()
	{
		$this->cart->update('foo', [
			'price' => 20.00,
		]);
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function it_throws_exception_when_removing_an_item_that_does_not_exist()
	{
		$this->cart->remove('foo');
	}

	/**
	 * @test
	 * @expectedException \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function it_throws_exception_when_getting_an_item_that_does_not_exist()
	{
		$this->cart->item('foo');
	}

}
