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

use Cartalyst\Cart\Storage\StorageInterface;

interface CookieInterface extends StorageInterface {

	/**
	 * Returns both session key and session instance.
	 *
	 * @return string
	 */
	public function getCookieKey();

	/**
	 * Return the time to live.
	 *
	 * @return int
	 */
	public function getTtl();

	/**
	 * Set the time to live.
	 *
	 * @param  int  $ttl
	 * @return void
	 */
	public function setTtl($ttl);

}
