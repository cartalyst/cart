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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2019, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Cart\Tests;

use Mockery as m;
use Cartalyst\Cart\Cart;
use Illuminate\Session\Store;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\FileSessionHandler;
use Cartalyst\Cart\Storage\IlluminateSession;

class CartTestEvents extends CartTestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Setup resources and dependencies.
     */
    protected function setUp(): void
    {
        $sessionHandler = new FileSessionHandler(new Filesystem(), __DIR__.'/storage/sessions', 120);

        $session = new IlluminateSession(new Store('cartalyst_cart_session', $sessionHandler));

        $this->dispatcher = m::mock('Illuminate\Contracts\Events\Dispatcher');

        $this->dispatcherMethod = method_exists($this->dispatcher, 'fire') ? 'fire' : 'dispatch';

        $this->cart = new Cart($session, $this->dispatcher);
    }

    /** @test */
    public function can_listen_to_the_added_event()
    {
        $this->dispatcher->shouldReceive($this->dispatcherMethod)->once()->with('cartalyst.cart.created', m::any());

        $this->dispatcher->shouldReceive($this->dispatcherMethod)->once()->with('cartalyst.cart.added', m::any());

        $this->cart->add(
            $this->createItem('Foobar 1', 125, 2)
        );
    }

    /** @test */
    public function can_listen_to_the_updated_event()
    {
        $this->dispatcher->shouldReceive($this->dispatcherMethod)->once()->with('cartalyst.cart.created', m::any());

        $this->dispatcher->shouldReceive($this->dispatcherMethod)->once()->with('cartalyst.cart.added', m::any());

        $this->dispatcher->shouldReceive($this->dispatcherMethod)->once()->with('cartalyst.cart.updated', m::any());

        $item = $this->cart->add(
            $this->createItem('Foobar 1', 125, 2)
        );

        $this->cart->update($item['rowId'], [
            'name' => 'Foo',
        ]);
    }

    /** @test */
    public function can_listen_to_the_removed_event()
    {
        $this->dispatcher->shouldReceive($this->dispatcherMethod)->once()->with('cartalyst.cart.created', m::any());

        $this->dispatcher->shouldReceive($this->dispatcherMethod)->once()->with('cartalyst.cart.added', m::any());

        $this->dispatcher->shouldReceive($this->dispatcherMethod)->once()->with('cartalyst.cart.removed', m::any());

        $item = $this->cart->add(
            $this->createItem('Foobar 1', 125, 2)
        );

        $this->cart->remove($item['rowId']);
    }

    /** @test */
    public function can_listen_to_the_cleared_event()
    {
        $this->dispatcher->shouldReceive($this->dispatcherMethod)->once()->with('cartalyst.cart.cleared', m::any());

        $this->cart->clear();
    }
}
