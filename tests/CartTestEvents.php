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
 * @version    1.1.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Cart\tests;

use Cartalyst\Cart\Cart;
use Cartalyst\Cart\Storage\IlluminateSession;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\FileSessionHandler;
use Illuminate\Session\Store;
use Mockery as m;

class CartTestEvents extends CartTestCase
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

    /**
     * Setup resources and dependencies
     */
    public function setUp()
    {
        $sessionHandler = new FileSessionHandler(new Filesystem, __DIR__.'/storage/sessions');

        $session = new IlluminateSession(new Store('cartalyst_cart_session', $sessionHandler));

        $this->dispatcher = m::mock('Illuminate\Events\Dispatcher');

        $this->cart = new Cart($session, $this->dispatcher);
    }

    /** @test */
    public function can_listen_to_the_add_events()
    {
        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.created', m::any());

        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.adding', m::any());

        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.added', m::any());

        $this->cart->add(
            $this->createItem('Foobar 1', 125, 2)
        );
    }

    /** @test */
    public function can_listen_to_the_update_events()
    {
        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.created', m::any());

        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.adding', m::any());

        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.added', m::any());

        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.updating', m::any());

        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.updated', m::any());

        $item = $this->cart->add(
            $this->createItem('Foobar 1', 125, 2)
        );

        $this->cart->update($item['rowId'], [
            'name' => 'Foo',
        ]);
    }

    /** @test */
    public function can_listen_to_the_removal_events()
    {
        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.created', m::any());

        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.adding', m::any());

        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.added', m::any());

        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.removing', m::any());

        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.removed', m::any());

        $item = $this->cart->add(
            $this->createItem('Foobar 1', 125, 2)
        );

        $this->cart->remove($item['rowId']);
    }

    /** @test */
    public function can_listen_to_the_clear_events()
    {
        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.clearing', m::any());

        $this->dispatcher->shouldReceive('fire')->once()->with('cartalyst.cart.cleared', m::any());

        $this->cart->clear();
    }
}
