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
 * @version    5.1.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Cartalyst\Cart\Storage;

use Illuminate\Session\Store as SessionStore;

class IlluminateSession implements StorageInterface
{
    /**
     * The key used in the Session.
     *
     * @var string
     */
    protected $key = 'cartalyst_cart';

    /**
     * The instance that is being used.
     *
     * @var string
     */
    protected $instance = 'main';

    /**
     * Session store object.
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * Creates a new Illuminate based Session driver for Cart.
     *
     * @param \Illuminate\Session\Store $session
     * @param string                    $instance
     * @param string                    $key
     *
     * @return void
     */
    public function __construct(SessionStore $session, $instance = null, $key = null)
    {
        $this->session = $session;

        $this->key = $key ?: $this->key;

        $this->instance = $instance ?: $this->instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * {@inheritdoc}
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->session->get($this->getSessionKey());
    }

    /**
     * {@inheritdoc}
     */
    public function put($value)
    {
        $this->session->put($this->getSessionKey(), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function has()
    {
        return $this->session->has($this->getSessionKey());
    }

    /**
     * {@inheritdoc}
     */
    public function forget()
    {
        $this->session->forget($this->getSessionKey());
    }

    /**
     * Returns both session key and session instance.
     *
     * @return string
     */
    protected function getSessionKey()
    {
        return "{$this->getKey()}.{$this->getInstance()}";
    }
}
