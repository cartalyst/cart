<?php namespace Cartalyst\Cartify;
/**
 * Part of the Cartify package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Cartify
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Cartify\Collections\CartCollection;
use Cartalyst\Cartify\Collections\ItemCollection;
use Cartalyst\Cartify\Exceptions\CartInvalidDataException;
use Cartalyst\Cartify\Exceptions\CartItemNotFoundException;
use Illuminate\Session\Store as SessionStorage;

class Cartify {

	/**
	 * The session driver used by Cartify.
	 *
	 * @var \Illuminate\Session\Store
	 */
	protected $session;

	/**
	 * Cart instance.
	 *
	 * @var string
	 */
	protected $instance = 'main';

	/**
	 * Constructor.
	 *
	 * @param  \Illuminate\Session\Store  $session
	 * @return void
	 */
	public function __construct(SessionStorage $session = null)
	{
		$this->session = $session;
	}

	/**
	 * Adds a new item to the cart.
	 *
	 * @param  string|Array  $id
	 * @param  string        $name
	 * @param  int           $quantity
	 * @param  float         $price
	 * @param  array         $options
	 * @return mixed
	 */
	public function add($id = null, $name = null, $quantity = null, $price = null, $options = array())
	{
		return $this->addItem($id, $name, $quantity, $price, $options);
	}

	/**
	 * Add multiple items to the cart.
	 *
	 * @param  array  $items
	 * @return bool
	 * @throws Cartalyst\Cartify\Exceptions\InvalidDataException
	 */
	public function addBatch($items)
	{
		if (is_array($items))
		{
			if ($this->isMulti($items))
			{
				foreach ($items as $item)
				{
					$options = ! empty($item['options']) ? $item['options'] : array();

					$this->add($item['id'], $item['name'], $item['quantity'], $item['price'], $options);
				}

				return true;
			}

			$options = ! empty($items['options']) ? $items['options'] : array();

			$this->add($items['items'], $items['name'], $items['quantity'], $items['price'], $options);

			return true;
		}

		throw new InvalidDataException;
	}

	/**
	 * Remove an item from the cart.
	 *
	 * @param  string  $rowId
	 * @return bool
	 * @throws Cartalyst\Cartify\Exceptions\CartItemNotFoundException
	 */
	public function remove($rowId)
	{
		// Check if the item exists
		if ( ! $this->itemExists($rowId))
		{
			throw new CartItemNotFoundException;
		}

		// Get the cart contents
		$cart = $this->getContent();

		// Remove the item from the cart
		$cart->forget($rowId);

		// Update the cart contents
		return $this->updateCart($cart);
	}

	/**
	 * Remove multiple items from the cart.
	 *
	 * @param  array  $items
	 * @return bool
	 * @throws Cartalyst\Cartify\Exceptions\InvalidDataException
	 */
	public function removeBatch($items)
	{
		if (is_array($items))
		{

		}

		throw new InvalidDataException;
	}

	/**
	 * Updates an item in the cart.
	 *
	 * @param  string  $rowId
	 * @param  array   $data
	 * @return bool
	 * @throws Cartalyst\Cartify\Exceptions\CartItemNotFoundException
	 */
	public function update($rowId, $data)
	{
		// Check if the item exists
		if ( ! $this->itemExists($rowId))
		{
			throw new CartItemNotFoundException;
		}

		# todo ..

	}

	/**
	 * Empties the cart.
	 *
	 * @return void
	 */
	public function destroy()
	{
		$this->updateCart(null);
	}

	/**
	 * Returns information about an item.
	 *
	 * @param  string  $rowId
	 * @return Cartalyst\Cartify\Collections\ItemCollection
	 * @throws Cartalyst\Cartify\Exceptions\CartItemNotFoundException
	 */
	public function getItem($rowId)
	{
		// Check if the item exists
		if ( ! $this->itemExists($rowId))
		{
			throw new CartItemNotFoundException;
		}

		// Get the cart contents
		$cart = $this->getContent();

		// Return the item
		return $cart->get($rowId);
	}






	protected function addItem($id, $name, $quantity, $price, $options = array())
	{
		// Validate the required parameters
		if (empty($id) or empty($name) or empty($quantity) or empty($price))
		{
			throw new CartInvalidDataException;
		}

		// Generate the unique row id
		$rowId = $this->generateRowId($id, $options);

		// Insert the item into the cart
		$this->createItem($rowId, $id, $name, $quantity, $price, $options);
	}


	protected function updateItem()
	{

	}

	protected function createItem($rowId, $id, $name, $quantity, $price, $options = array())
	{
		// Get the cart contents
		$cart = $this->getContent();

		// Create a new item
		$newRow = new ItemCollection(array(
			'rowId' => $rowId,
			'id'    => $id,
			'name'  => $name,
			'quantity' => $quantity,
			'price'    => $price,
			'options'  => $options,
			'subtotal' => $quantity * $price,
		));

		// Add the item to the cart
		$cart->put($rowId, $newRow);

		$this->updateCart($cart);

		return $cart;
	}


	/**
	 * Updates the cart.
	 *
	 * @param  Cartalyst\Cartify\Collections\CartCollection
	 * @return void
	 */
	public function updateCart($cart)
	{
		$instance = $this->getInstance();

		$this->session->put($instance, $cart);
	}

	/**
	 * Return the cart total.
	 *
	 * @return float
	 */
	public function getTotal()
	{
		$items = $this->getContent();

		$total = 0;

		foreach ($items as $item)
		{
			$total += $item->subtotal;
		}

		return $total;
	}

	/**
	 * Return the total items on the cart.
	 *
	 * @return int
	 */
	public function getTotalItems()
	{
		$items = $this->getContent();

		return $items->count();
	}

	/**
	 * Return the cart contents.
	 *
	 * @return Cartalyst\Cartify\Collections\CartCollection
	 */
	public function getContent()
	{
		$instance = $this->getInstance();

		return $this->session->has($instance) ? $this->session->get($instance) : new CartCollection;
	}

	/**
	 * Return the current cart instance.
	 *
	 * @return string
	 */
	public function getInstance()
	{
		return "cartify.{$this->instance}";
	}

	/**
	 * Change the cart instance.
	 *
	 * @return Cartalyst\Cartify\Cartify
	 */
	public function setInstance($instance)
	{
		$this->instance = $instance;

		return $this;
	}

	/**
	 * Check if an item exists in the cart.
	 *
	 * @param  string  $itemRowId
	 * @return bool
	 */
	protected function itemExists($rowId)
	{
		return $this->getContent()->has($rowId);
	}

	/**
	 * Generate a unique identifier base on the item data.
	 *
	 * @param  string  $id
	 * @param  array   $options
	 * @return string
	 */
	protected function generateRowId($id, $options)
	{
		return md5($id.serialize($options));
	}

	/**
	 * Checks if the provided array is a multidimensional array.
	 *
	 * @param  array  $array
	 * @return bool
	 */
	protected function isMulti($array)
	{
		$array = array_shift($array);

		return is_array($array);
	}

}
