<?php namespace Cartalyst\Cart\Storage\Sessions;
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

use Cartalyst\Cart\Storage\Sessions\SessionInterface;
use Illuminate\Cookie\CookieJar;
use Illuminate\Session\Store as SessionStore;

class NativeSession extends IlluminateSession implements SessionInterface {

	/**
	 * Creates a new Native Session driver for Cart.
	 *
	 * @param  \Illuminate\Session\Store  $session
	 * @param  string  $key
	 * @param  string  $instance
	 * @param  array   $config
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
