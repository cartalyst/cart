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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

interface StorageInterface {

	/**
	 * Returns the session key.
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
	 * Set the instance.
	 *
	 * @param  string  $instance
	 * @return void
	 */
	public function setInstance($instance);

	/**
	 * Returns all the available instances of the key.
	 *
	 * @return array
	 */
	public function instances();

	/**
	 * Get the value from the storage.
	 *
	 * @return mixed
	 */
	public function get();

	/**
	 * Put a value.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	public function put($value);

	/**
	 * Checks if an attribute is defined.
	 *
	 * @return bool
	 */
	public function has();

	/**
	 * Remove the storage.
	 *
	 * @return void
	 */
	public function forget();

}
