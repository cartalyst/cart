<?php namespace Cartalyst\Cart\Storage;
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
 * @version    1.1.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Session\Store as SessionStore;

class IlluminateSession implements StorageInterface {

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
	 * @param  \Illuminate\Session\Store  $session
	 * @param  string  $key
	 * @param  string  $instance
	 * @return void
	 */
	public function __construct(SessionStore $session, $key = null, $instance = null)
	{
		$this->session = $session;

		if (isset($key))
		{
			$this->key = $key;
		}

		if (isset($instance))
		{
			$this->instance = $instance;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * {@inheritDoc}
	 */
	public function identify()
	{
		return $this->instance;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get()
	{
		return $this->session->get($this->getSessionKey());
	}

	/**
	 * {@inheritDoc}
	 */
	public function put($value)
	{
		$this->session->put($this->getSessionKey(), $value);
	}

	/**
	 * {@inheritDoc}
	 */
	public function has()
	{
		return $this->session->has($this->getSessionKey());
	}

	/**
	 * {@inheritDoc}
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
		return "{$this->getKey()}.{$this->identify()}";
	}

}
