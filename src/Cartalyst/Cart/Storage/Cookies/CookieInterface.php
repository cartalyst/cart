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
	 * Put a value in the Cart cookie.
	 *
	 * @param  mixed  $value
	 * @param  int    $minutes
	 * @return void
	 */
	public function put($value, $minutes);

	/**
	 * Put a value in the Cart cookie forever.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	public function forever($value);

	/**
	 * Get the Cart cookie value.
	 *
	 * @return mixed
	 */
	public function get();

	/**
	 * Remove the Cart cookie.
	 *
	 * @return void
	 */
	public function forget();

}
