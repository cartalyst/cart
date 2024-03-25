<?php

/*
 * Part of the Cart package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Cart
 * @version    8.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2024, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Cartalyst\Cart\Tests;

class CartTestExceptions extends CartTestCase
{
    /**
     * @test
     */
    public function it_throws_exception_when_missing_a_required_index()
    {
        $this->expectException('Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException');

        $this->cart->add([
            'name'     => 'foo',
            'price'    => 20.00,
            'quantity' => 5,
        ]);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_missing_a_required_index_on_attributes()
    {
        $this->expectException('Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException');

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
     */
    public function it_throws_exception_when_adding_single_item_with_invalid_quantity()
    {
        $this->expectException('Cartalyst\Cart\Exceptions\CartInvalidQuantityException');

        $this->cart->add([
            'id'       => 'foobar1',
            'name'     => 'Foobar 1',
            'quantity' => -2,
            'price'    => 125.00,
        ]);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_adding_single_item_with_invalid_price()
    {
        $this->expectException('Cartalyst\Cart\Exceptions\CartInvalidPriceException');

        $this->cart->add([
            'id'       => 'foobar1',
            'name'     => 'Foobar 1',
            'quantity' => 1,
            'price'    => 'foo',
        ]);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_invalid_quantity_is_passed()
    {
        $this->expectException('Cartalyst\Cart\Exceptions\CartInvalidQuantityException');

        $this->cart->add([
            'id'       => 1,
            'name'     => 'foo',
            'price'    => 20.00,
            'quantity' => 'bar',
        ]);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_invalid_price_is_passed()
    {
        $this->expectException('Cartalyst\Cart\Exceptions\CartInvalidPriceException');

        $this->cart->add([
            'id'       => 1,
            'name'     => 'foo',
            'price'    => 'bar',
            'quantity' => 5,
        ]);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_invalid_attributes_are_passed()
    {
        $this->expectException('Cartalyst\Cart\Exceptions\CartInvalidAttributesException');

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
     */
    public function it_throws_exception_updating_an_item_that_does_not_exist()
    {
        $this->expectException('Cartalyst\Cart\Exceptions\CartItemNotFoundException');

        $this->cart->update('foo', [
            'price' => 20.00,
        ]);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_removing_an_item_that_does_not_exist()
    {
        $this->expectException('Cartalyst\Cart\Exceptions\CartItemNotFoundException');

        $this->cart->remove('foo');
    }

    /**
     * @test
     */
    public function it_throws_exception_when_getting_an_item_that_does_not_exist()
    {
        $this->expectException('Cartalyst\Cart\Exceptions\CartItemNotFoundException');

        $this->cart->item('foo');
    }
}
