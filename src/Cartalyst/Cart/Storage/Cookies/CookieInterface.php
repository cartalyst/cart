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
	 * Returns the cookie key.
	 *
	 * @return string
	 */
	public function getKey();

	/**
	 * Return the session instance.
	 *
	 * @return string
	 */
	public function getInstance();

	/**
	 * Set the session instance.
	 *
	 * @param  string  $instance
	 * @return void
	 */
	public function setInstance($instance);

	/**
	 * Returns all the available session instances of the session key.
	 *
	 * @return array
	 */
	public function instances();

	/**
	 * Returns both session key and session instance.
	 *
	 * @return string
	 */
	public function getCookieKey();

	/**
	 * Get the Cart cookie value.
	 *
	 * @return mixed
	 */
	public function get();

	/**
	 * Put a value in the Cart cookie.
	 *
	 * @param  mixed  $value
	 * @param  int    $minutes
	 * @return void
	 */
	public function put($value, $minutes = null);

	/**
	 * Put a value in the Cart cookie forever.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	public function forever($value);

	/**
	 * Checks if an attribute is defined.
	 *
	 * @return bool
	 */
	public function has();

	/**
	 * Remove the Cart cookie.
	 *
	 * @return void
	 */
	public function forget();

}
