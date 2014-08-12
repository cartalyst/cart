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

use Illuminate\Cookie\CookieJar;
use Illuminate\Session\Store as SessionStore;

class NativeSession extends IlluminateSession implements StorageInterface {

	/**
	 * Creates a new Native Session driver for Cart.
	 *
	 * @param  \Illuminate\Session\Store  $session
	 * @param  string  $key
	 * @param  string  $instance
	 * @param  array  $config
	 * @return void
	 */
	public function __construct(SessionStore $session, $key = null, $instance = null, $config = [])
	{
		parent::__construct($session, $key, $instance);

		// Cookie configuration
		$lifetime = array_get($config, 'lifetime', 120);
		$path     = array_get($config, 'path', '/');
		$domain   = array_get($config, 'domain', null);
		$secure   = array_get($config, 'secure', false);
		$httpOnly = array_get($config, 'httpOnly', true);

		if ($cookieId = array_get($_COOKIE, $session->getName()))
		{
			$session->setId($cookieId);

			$session->setName($cookieId);
		}

		$cookie = with(new CookieJar)->make($session->getName(), $session->getId(), $lifetime, $path, $domain, $secure, $httpOnly);

		setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());

		$session->start();
	}

	/**
	 * Called upon destruction of the native session handler.
	 *
	 * @return void
	 */
	public function __destruct()
	{
		$this->session->save();
	}

}
