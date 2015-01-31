<?php

/**
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
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Cart\Storage;

interface StorageInterface
{
    /**
     * Returns the session key.
     *
     * @return string
     */
    public function getKey();

    /**
     * Returns the session instance identifier.
     *
     * @return string
     */
    public function getInstance();

    /**
     * Sets the session instance identifier.
     *
     * @param  string  $instance
     * @return string
     */
    public function setInstance($instance);

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
