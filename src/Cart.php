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
 * @version    4.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Cartalyst\Cart;

use Cartalyst\Collections\Collection;
use Illuminate\Contracts\Events\Dispatcher;
use Cartalyst\Cart\Storage\StorageInterface;
use Cartalyst\Cart\Collections\CartCollection;

class Cart
{
    /**
     * The storage driver used by Cart.
     *
     * @var \Cartalyst\Cart\Storage\StorageInterface
     */
    protected $storage;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * The cart collection instance.
     *
     * @var \Cartalyst\Cart\Collections\CartCollection
     */
    protected $cart;

    /**
     * Flag for whether we should fire events or not.
     *
     * @var bool
     */
    protected $fireEvents = true;

    /**
     * Constructor.
     *
     * @param \Cartalyst\Cart\Storage\StorageInterface $storage
     * @param \Illuminate\Events\Dispatcher            $dispatcher
     *
     * @return void
     */
    public function __construct(StorageInterface $storage, Dispatcher $dispatcher)
    {
        $this->storage = $storage;

        $this->dispatcher = $dispatcher;
    }

    /**
     * Returns the Cart instance identifier.
     *
     * @return mixed
     */
    public function getInstance()
    {
        return $this->storage->getInstance();
    }

    /**
     * Sets the Cart instance identifier.
     *
     * @param mixed $instance
     *
     * @return void
     */
    public function setInstance($instance)
    {
        $this->storage->setInstance($instance);
    }

    /**
     * Returns the cart contents.
     *
     * @return \Cartalyst\Cart\Collections\CartCollection
     */
    public function items()
    {
        if ($this->cart) {
            return $this->cart;
        }

        if ($this->storage->has()) {
            return $this->cart = $this->storage->get()->setCart($this);
        }

        return $this->cart = $this->newCartCollection();
    }

    /**
     * Empties the cart.
     *
     * @return void
     */
    public function clear()
    {
        $this->storage->put($this->cart = null);

        // Fire the 'cartalyst.cart.cleared' event
        $this->fire('cleared', $this);
    }

    /**
     * Synchronizes a collection of data with the cart.
     *
     * @param \Cartalyst\Collections\Collection $items
     *
     * @return void
     */
    public function sync(Collection $items)
    {
        // Turn events off
        $this->disableEvents();

        foreach ($items->all() as $item) {
            $this->add($item);
        }

        // Turn events on
        $this->enableEvents();
    }

    /**
     * Returns the storage driver.
     *
     * @return mixed
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Sets the storage driver.
     *
     * @param \Cartalyst\Cart\Storage\StorageInterface $storage
     *
     * @return void
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Returns the event dispatcher instance.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Sets the event dispatcher instance.
     *
     * @param \Illuminate\Events\Dispatcher $dispatcher
     *
     * @return void
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Enable event fire
     *
     * @return void
     */
    public function enableEvents()
    {
        $this->fireEvents = true;
    }

    /**
     * Disable event fire
     *
     * @return void
     */
    public function disableEvents()
    {
        $this->fireEvents = false;
    }

    /**
     * Fires an event.
     *
     * @param string $event
     * @param mixed  $data
     *
     * @return void
     */
    public function fire($event, $data)
    {
        // Check if we should fire events
        if ($this->fireEvents) {
            $this->dispatcher->dispatch("cartalyst.cart.{$event}", $data);
        }
    }

    /**
     * Handle dynamic calls into CartCollection.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->items(), $method], $parameters);
    }

    /**
     * Creates a new cart collection instance.
     *
     * @return \Cartalyst\Cart\Collections\CartCollection
     */
    protected function newCartCollection()
    {
        $cart = (new CartCollection())->setCart($this);

        $this->storage->put($cart);

        // Fire the 'cartalyst.cart.created' event
        $this->fire('created', $cart);

        return $cart;
    }
}
