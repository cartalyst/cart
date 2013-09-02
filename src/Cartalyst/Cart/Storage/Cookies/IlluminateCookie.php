<?php namespace Cartalyst\Cart\Storage\Cookies;
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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Cart\Storage\Cookies\CookieInterface;
use Illuminate\Container\Container;
use Illuminate\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Cookie;

class IlluminateCookie implements CookieInterface {

	/**
	 * The key used in the Cookie.
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
	 * The cookie object.
	 *
	 * @var \Illuminate\Cookie\CookieJar
	 */
	protected $jar;

	/**
	 * The cookie to be stored.
	 *
	 * @var \Symfony\Component\HttpFoundation\Cookie
	 */
	protected $cookie;

	/**
	 * Creates a new cookie instance.
	 *
	 * @param  \Illuminate\Cookie\CookieJar  $jar
	 * @param  string  $key
	 * @param  string  $instance
	 * @return void
	 */
	public function __construct(CookieJar $jar, $key = null, $instance = null)
	{
		$this->jar = $jar;

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
	 * Returns the cookie key.
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Return the cookie instance.
	 *
	 * @return string
	 */
	public function getInstance()
	{
		return $this->instance;
	}

	/**
	 * Set the cookie instance.
	 *
	 * @param  string  $instance
	 * @return void
	 */
	public function setInstance($instance)
	{
		$this->instance = $instance;
	}

	/**
	 * Returns both cookie key and cookie instance.
	 *
	 * @return string
	 */
	public function getCookieKey()
	{
		$key = $this->getKey();

		$instance = $this->getInstance();

		return "{$key}.{$instance}";
	}

	/**
	 * Returns all the available cookie instances of the cookie key.
	 *
	 * @return array
	 */
	public function instances()
	{
		return $this->jar->get($this->getKey());
	}

	/**
	 * Get the Cart cookie value.
	 *
	 * @return mixed
	 */
	public function get()
	{
		return $this->jar->get($this->getCookieKey());
	}

	/**
	 * Put a value in the Cart cookie.
	 *
	 * @param  mixed  $value
	 * @param  int    $minutes
	 * @return void
	 */
	public function put($value, $minutes = null)
	{
		$this->cookie = $this->jar->make($this->getCookieKey(), $value, $minutes);
	}

	/**
	 * Put a value in the Cart cookie forever.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	public function forever($value)
	{
		$this->cookie = $this->jar->forever($this->getCookieKey(), $value);
	}

	/**
	 * Checks if an attribute is defined.
	 *
	 * @return bool
	 */
	public function has()
	{
		return $this->jar->has($this->getCookieKey());
	}

	/**
	 * Remove the Cart cookie.
	 *
	 * @return void
	 */
	public function forget()
	{
		$this->cookie = $this->jar->forget($this->getCookieKey());
	}

	/**
	 * Return the cookie headers.
	 *
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	public function getHeaders()
	{
		return $this->cookie;
	}

}
