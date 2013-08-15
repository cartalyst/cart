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
use Cartalyst\Cartify\Collections\ItemOptionsCollection;
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
		if (is_array($id))
		{
			if ($this->isMulti($id))
			{
				foreach ($id as $item)
				{
					$options = ! empty($item['options']) ? $item['options'] : array();

					$this->add($item['id'], $item['name'], $item['quantity'], $item['price'], $options);
				}

				return true;
			}

			$options = ! empty($id['options']) ? $id['options'] : array();

			$this->add($id['id'], $id['name'], $id['quantity'], $id['price'], $options);

			return true;
		}

		return $this->addItem($id, $name, $quantity, $price, $options);
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
		if (is_array($rowId))
		{
			foreach ($rowId as $item)
			{
				$this->remove($item);
			}

			return true;
		}

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
	 * Updates an item in the cart.
	 *
	 * @param  string  $rowId
	 * @param  array   $attributes
	 * @return bool
	 * @throws Cartalyst\Cartify\Exceptions\CartItemNotFoundException
	 */
	public function update($rowId, $attributes = null)
	{
		if (is_array($rowId))
		{
			foreach ($rowId as $item => $attribute)
			{
				$this->update($item, $attribute);
			}

			return true;
		}

		// Check if the item exists
		if ( ! $this->itemExists($rowId))
		{
			throw new CartItemNotFoundException;
		}

		// Get the cart contents
		$cart = $this->getContent();

		// Get the item we want to update
		$row = $cart->get($rowId);

		// Do we have multiple item attributes?
		if (is_array($attributes))
		{
			foreach ($attributes as $key => $value)
			{
				if ($key === 'quantity')
				{
					$value = round($value);
				}

				$row->put($key, $value);
			}
		}

		// We are probably updating the quantity
		else
		{
			$row->put('quantity', (int) round($attributes));
		}

		// Should we update the item subtotal?
		if ( ! is_null(array_keys($attributes, array('quantity', 'price'))))
		{
			$row->put('subtotal', (float) $row->quantity * $row->price);
		}

		// If quantity is less than one, we remove the item
		if ($row->quantity < 1)
		{
			$cart->forget($rowId);
		}
		else
		{
			$cart->put($rowId, $row);
		}

		return $cart;
	}

	/**
	 * Empties the cart.
	 *
	 * @return void
	 */
	public function destroy()
	{
		// Update the cart contents
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

		// Get the cart contents
		$cart = $this->getContent();

		// Generate the unique row id
		$rowId = $this->generateRowId($id, $options);

		// Make sure that the quantity value is rounded
		$quantity = round($quantity);

		if ($this->itemExists($rowId))
		{
			$row = $this->getItem($rowId);

			$row->put('quantity', $row->quantity + $quantity);
		}
		else
		{
			// Create a new item
			$row = new ItemCollection(array(
				'rowId'    => $rowId,
				'id'       => $id,
				'name'     => $name,
				'quantity' => $quantity,
				'price'    => $price,
				'options'  => new ItemOptionsCollection($options),
				'subtotal' => $quantity * $price,
			));
		}

		// Add the item to the cart
		$cart->put($rowId, $row);

		// Update the cart contents
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

		return (float) $total;
	}

	/**
	 * Return the total items on the cart.
	 *
	 * @return int
	 */
	public function getTotalItems()
	{
		$items = $this->getContent();

		return (int) $items->count();
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
	 * Return all the cart instances.
	 *
	 * @return array
	 */
	public function getInstances()
	{
		return $this->session->get('cartify');
	}

	/**
	 * Change the cart instance.
	 *
	 * @return Cartalyst\Cartify\Cartify
	 */
	public function instance($instance)
	{
		$this->instance = $instance;

		return $this;
	}

	/**
	 * Remove the cart instance.
	 *
	 * @return bool
	 */
	public function forgetInstance($instance)
	{
		$this->session->forget("cartify.{$instance}");

		return true;
	}

	/**
	 * Check if an item exists in the cart.
	 *
	 * @param  string  $rowId
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
