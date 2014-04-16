<?php namespace Cartalyst\Cart\Storage;
/**
 * Part of the Cart package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Cart
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
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
	 * Returns the session key.
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Return the session instance.
	 *
	 * @return string
	 */
	public function identify()
	{
		return $this->instance;
	}

	/**
	 * Returns both session key and session instance.
	 *
	 * @return string
	 */
	public function getSessionKey()
	{
		$key = $this->getKey();

		$instance = $this->identify();

		return "{$key}.{$instance}";
	}

	/**
	 * Get the session value.
	 *
	 * @return mixed
	 */
	public function get()
	{
		return $this->session->get($this->getSessionKey());
	}

	/**
	 * Put a value in the session.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	public function put($value)
	{
		$this->session->put($this->getSessionKey(), $value);
	}

	/**
	 * Checks if an attribute is defined.
	 *
	 * @return bool
	 */
	public function has()
	{
		return $this->session->has($this->getSessionKey());
	}

	/**
	 * Remove the Cart session.
	 *
	 * @return void
	 */
	public function forget()
	{
		$this->session->forget($this->getSessionKey());
	}

}
