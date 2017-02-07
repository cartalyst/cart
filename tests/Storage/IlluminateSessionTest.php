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
 * @version    2.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Cart\Tests\Storage;

use Mockery as m;
use Cartalyst\Cart\Tests\CartTestCase;
use Cartalyst\Cart\Storage\IlluminateSession;

class IlluminateSessionTest extends CartTestCase
{
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
    public function it_can_get_cart_session_key_and_cart_identity()
    {
        $this->assertEquals($this->cart->getStorage()->getInstance(), 'cart');
        $this->assertEquals($this->cart->getStorage()->getKey(), 'cartalyst_cart');
        $this->assertInstanceOf('Cartalyst\Cart\Storage\IlluminateSession', $this->cart->getStorage());

        $this->cart->add($this->createItem('Foobar 1', 125, 2));

        $this->assertCount(1, $this->cart->items());

        $this->cart->getStorage()->forget();

        $this->assertNull($this->cart->getStorage()->get());
    }

    /** @test */
    public function it_can_set_cart_session_key_and_cart_identity_on_initialization()
    {
        $session = new IlluminateSession(m::mock('Illuminate\Session\Store'), 'instance', 'cart');

        $this->assertEquals($session->getKey(), 'cart');
        $this->assertEquals($session->getInstance(), 'instance');
    }
}
