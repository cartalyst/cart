<?php namespace Cartalyst\Cart\Storage\Database;
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

use Cartalyst\Cart\Storage\Database\DatabaseInterface;
use Illuminate\Database\DatabaseManager;

class IlluminateDatabase implements DatabaseInterface {

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
	protected $database;

	/**
	 * Creates a new Illuminate based Database driver for Cart.
	 *
	 * @param  \Illuminate\Session\Store  $database
	 * @param  string  $key
	 * @param  string  $instance
	 * @return void
	 */
	public function __construct(DatabaseManager $database, $key = null, $instance = null)
	{
		$this->database = $database;

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
	public function getInstance()
	{
		return $this->instance;
	}

	/**
	 * Set the session instance.
	 *
	 * @param  string  $instance
	 * @return void
	 */
	public function setInstance($instance)
	{
		$this->instance = $instance;
	}

	/**
	 * Returns all the available session instances of the session key.
	 *
	 * @return array
	 */
	public function instances()
	{
		return $this->database->get($this->getKey());
	}

	/**
	 * Returns both session key and session instance.
	 *
	 * @return string
	 */
	public function getSessionKey()
	{
		$key = $this->getKey();

		$instance = $this->getInstance();

		return "{$key}.{$instance}";
	}

	/**
	 * Get the session value.
	 *
	 * @return mixed
	 */
	public function get()
	{
		$data = $this->database->table('cart')
			->where('instance', $this->getInstance())
			->where('session_id', \Session::getId())
			->get();

		$collection = new \Cartalyst\Cart\Collections\CartCollection;

		foreach ($data as $item)
		{
			$collection->put($item->row_id, new \Cartalyst\Cart\Collections\ItemCollection(array(
				'rowId'    => $item->row_id,
				'id'       => $item->id,
				'name'     => $item->name,
				'quantity' => (float) $item->quantity,
				'price'    => (float) $item->price,
				'tax'      => $item->tax,
				'options'  => new \Cartalyst\Cart\Collections\ItemOptionsCollection,
			)));
		}

		return $collection;
	}

	/**
	 * Put a value in the session.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	public function put($items)
	{
		var_dump($items);
		foreach ($items as $item)
		{
			$this->database->table('cart')->insert(array(
				'session_id' => \Session::getId(),
				'instance' => $this->getInstance(),
				'row_id' => $item->rowId,
				'name' => $item->name,
				'price' => $item->price,
				'quantity' => $item->quantity,
			));
		}


echo 'y';

		die;
		#$this->database->put($this->getSessionKey(), $value);
	}

	/**
	 * Checks if an attribute is defined.
	 *
	 * @return bool
	 */
	public function has()
	{
		return true; // for now
		#return $this->database->has($this->getSessionKey());
	}

	/**
	 * Remove the Sentry session.
	 *
	 * @return void
	 */
	public function forget()
	{
		echo 'yyy';
		die;
		$this->database->forget($this->getSessionKey());
	}

	/**
	 * Return the session headers.
	 *
	 * @return \Illuminate\Session\Store
	 */
	public function getHeaders()
	{
		return $this->database;
	}

}
