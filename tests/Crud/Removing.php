<?php

/**
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
 * @version    1.1.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Cart\tests\Crud;

use Cartalyst\Cart\Tests\CartTestCase;

class Removing extends CartTestCase
{
    /** @test */
    public function it_can_remove_a_single_item()
    {
        $item1 = $this->createItem('Foobar 1', 120, 7);
        $item2 = $this->createItem('Foobar 2', 120, 3);

        $this->cart->add([$item1, $item2]);

        $this->assertEquals($this->cart->quantity(), 10);
        $this->assertCount(2, $this->cart->items());

        $this->cart->remove('b37f673e46a33038305c1dc411215c07');

        $this->assertEquals($this->cart->quantity(), 3);
        $this->assertCount(1, $this->cart->items());
        $this->assertEmpty($this->cart->find(['b37f673e46a33038305c1dc411215c07']));
    }

    /** @test */
    public function it_can_remove_multiple_items()
    {
        $item1 = $this->createItem('Foobar 1', 120, 7);
        $item2 = $this->createItem('Foobar 2', 120, 3);

        $this->cart->add([$item1, $item2]);

        $this->cart->remove(['b37f673e46a33038305c1dc411215c07', '07d732dbcc3ce0752ac74870d6fa2194']);

        $this->assertEmpty($this->cart->items());
    }
}
