<?php namespace Cartalyst\Cart;
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

use Cartalyst\Cart\Collections\CartCollection;
use Cartalyst\Cart\Collections\ItemAttributesCollection;
use Cartalyst\Cart\Collections\ItemCollection;
use Cartalyst\Cart\Exceptions\CartInvalidAttributesException;
use Cartalyst\Cart\Exceptions\CartInvalidPriceException;
use Cartalyst\Cart\Exceptions\CartInvalidQuantityException;
use Cartalyst\Cart\Exceptions\CartItemNotFoundException;
use Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException;
use Cartalyst\Cart\Storage\StorageInterface;
use Illuminate\Events\Dispatcher;

class Cart extends CartCollection {

	/**
	 * Holds the Cart identifier.
	 *
	 * @var mixed
	 */
	protected $id;

	/**
	 * The storage driver used by Cart.
	 *
	 * @var \Cartalyst\Cart\Storage\StorageInterface
	 */
	protected $storage;

	/**
	 * The event dispatcher instance.
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * Holds all the required indexes.
	 *
	 * @var array
	 */
	protected $requiredIndexes = array(
		'id',
		'name',
		'price',
		'quantity',
	);

	/**
	 * Holds all the reserved indexes.
	 *
	 * @var array
	 */
	protected $reservedIndexes = array(
		'price',
		'quantity',
	);

	/**
	 * Holds all the cart conditions.
	 *
	 * @var array
	 */
	protected $conditions = array();

	/**
	 * Constructor.
	 *
	 * @param  mixed  $id
	 * @param  \Cartalyst\Cart\Storage\StorageInterface  $storage
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function __construct($id, StorageInterface $storage, Dispatcher $dispatcher)
	{
		$this->id = $id;

		$this->storage = $storage;

		$this->dispatcher = $dispatcher;
	}

	/**
	 * Returns the Cart identifier.
	 *
	 * @return mixed
	 */
	public function getIdentity()
	{
		return $this->id;
	}

	/**
	 * Sets the Cart identifier.
	 *
	 * @param  mixed  $id
	 * @return void
	 */
	public function setIdentity($id)
	{
		$this->id = $id;
	}

	/**
	 * Adds a single or multiple items to the cart.
	 *
	 * @param  array  $item
	 * @return mixed
	 * @throws \Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException
	 * @throws \Cartalyst\Cart\Exceptions\CartInvalidQuantityException
	 * @throws \Cartalyst\Cart\Exceptions\CartInvalidPriceException
	 * @throws \Cartalyst\Cart\Exceptions\CartInvalidAttributesException
	 */
	public function add($item)
	{
		// Do we have multiple items?
		if ($this->isMulti($item))
		{
			foreach ($item as $_item)
			{
				$this->add($_item);
			}

			return true;
		}

		// Validate the required parameters
		foreach ($this->requiredIndexes as $parameter)
		{
			if ( ! isset($item[$parameter]))
			{
				throw new CartMissingRequiredIndexException($parameter);
			}
		}

		// Make sure the quantity is an integer
		$quantity = (int) $item['quantity'];

		// Check if the quantity value is correct
		if ( ! is_numeric($quantity) or $quantity < 1)
		{
			throw new CartInvalidQuantityException;
		}

		$price = $item['price'];

		// Check if the price value is correct
		if ( ! is_numeric($price))
		{
			throw new CartInvalidPriceException;
		}

		// Get this item attributes
		$attributes = array_get($item, 'attributes', array());

		// Validate the attributes
		if ( ! is_array($attributes))
		{
			throw new CartInvalidAttributesException;
		}

		// Generate the unique row id
		$rowId = $this->generateRowId($item['id'], $attributes);

		// Check if the item already exists on the cart
		if ($this->itemExists($rowId))
		{
			// Get the item
			$row = $this->item($rowId);

			// Update the item quantity
			$row->put('quantity', $row->get('quantity') + $quantity);
		}
		else
		{
			// Prepare the attributes
			$attributes = $this->prepareAttributes($attributes);

			// Create a new item
			$row = new ItemCollection(array_merge($item, compact('rowId', 'quantity', 'price', 'attributes')));
		}

		// Assign item conditions
		$row->condition(array_get($item, 'conditions', array()));

		// Set the item price
		$row->setPrice($price);

		// Get the cart contents
		$cart = $this->items();

		// Add the item to the cart
		$cart->put($rowId, $row);

		// Update the cart contents
		$this->updateCart($cart);

		// Fire the 'cartalyst.cart.added' event
		$this->dispatcher->fire('cartalyst.cart.added', array($this->item($rowId), $this->getIdentity()));

		return $cart;
	}

	/**
	 * Remove a single or multiple items from the cart.
	 *
	 * @param  mixed  $items
	 * @return bool
	 * @throws \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function remove($items)
	{
		foreach ((array) $items as $rowId)
		{
			// Check if the item exists
			if ( ! $this->itemExists($rowId))
			{
				throw new CartItemNotFoundException;
			}

			// Get the item information
			$item = $this->item($rowId);

			// Get the cart contents
			$cart = $this->items();

			// Remove the item from the cart
			$cart->forget($rowId);

			// Fire the 'cartalyst.cart.removed' event
			$this->dispatcher->fire('cartalyst.cart.removed', array($item, $this->getIdentity()));
		}

		$this->updateCart($cart);

		return true;
	}

	/**
	 * Update a single or multiple items that are on the cart.
	 *
	 * @param  string  $rowId
	 * @param  array   $attributes
	 * @return mixed
	 * @throws \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function update($rowId, $attributes = null)
	{
		// Do we have an array of items to be updated?
		if (is_array($rowId))
		{
			foreach ($rowId as $item => $attributes)
			{
				$this->update($item, $attributes);
			}

			return true;
		}

		// Check if the item exists
		if ( ! $this->itemExists($rowId))
		{
			throw new CartItemNotFoundException;
		}

		// Get the cart contents
		$cart = $this->items();

		// Get the item we want to update
		$row = $cart->get($rowId);

		// Do we have multiple item attributes?
		if (is_array($attributes))
		{
			foreach ($attributes as $key => $value)
			{
				if ($key === 'price')
				{
					$value = (float) $value;
				}
				elseif ($key === 'quantity')
				{
					$value = (int) $value;
				}
				elseif ($key === 'attributes')
				{
					$value = $this->prepareAttributes($value);
				}

				$row->put($key, $value);
			}
		}

		// We are probably updating the item quantity
		else
		{
			$row->put('quantity', (int) $attributes);
		}

		// Reset the item conditions
		$row->clearConditions();

		// Assign conditions to the item
		$row->condition(array_get($row, 'conditions'));

		// Set the item price
		$row->setPrice($row->get('price'));

		// Remove the item if the quantity is less than one
		if ($row->get('quantity') < 1)
		{
			$this->remove($rowId);
		}
		else
		{
			// Update the item
			$cart->put($rowId, $row);

			// Fire the 'cartalyst.cart.updated' event
			$this->dispatcher->fire('cartalyst.cart.updated', array($this->item($rowId), $this->getIdentity()));
		}

		return $cart;
	}

	/**
	 * Empties the cart.
	 *
	 * @return void
	 */
	public function clear()
	{
		$this->updateCart();

		// Fire the 'cartalyst.cart.cleared' event
		$this->dispatcher->fire('cartalyst.cart.cleared', $this->getIdentity());
	}

	/**
	 * Returns information about the provided item.
	 *
	 * @param  string  $rowId
	 * @return \Cartalyst\Cart\Collections\ItemCollection
	 * @throws \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function item($rowId)
	{
		// Check if the item exists
		if ( ! $this->itemExists($rowId))
		{
			throw new CartItemNotFoundException;
		}

		// Return the item
		return $this->items()->get($rowId);
	}

	/**
	 * Returns the cart contents.
	 *
	 * @return \Cartalyst\Cart\Collections\CartCollection
	 */
	public function items()
	{
		return $this->storage->has() ? $this->storage->get() : new CartCollection;
	}

	/**
	 * Returns the conditions order.
	 *
	 * @return array
	 */
	public function getConditionsOrder()
	{
		return $this->items()->getConditionsOrder();
	}

	/**
	 * Sets the conditions order.
	 *
	 * @param  array  $order
	 * @return void
	 */
	public function setConditionsOrder(array $order)
	{
		$cart = $this->items();

		$cart->conditionsOrder = $order;

		$this->updateCart($cart);
	}

	/**
	 * Sets the items conditions order.
	 *
	 * @param  array  $order
	 * @return void
	 */
	public function setItemsConditionsOrder(array $order)
	{
		$cart = $this->items();

		foreach ($cart as $key => $item)
		{
			$item->setConditionsOrder($order);

			$cart[$key] = $item;
		}

		$this->updateCart($cart);
	}

	/**
	 * Sets a new condition.
	 *
	 * @param  mixed  $condition
	 * @return void
	 */
	public function condition($condition)
	{
		if (empty($condition)) return;

		if (is_array($condition))
		{
			foreach ($condition as $_condition)
			{
				$this->condition($_condition);
			}

			return;
		}

		$cart = $this->items();

		if ($condition->validate($this))
		{
			$cart->conditions[$condition->get('name')] = $condition;
		}

		$this->updateCart($cart);
	}

	/**
	 * Clear the conditions.
	 *
	 * @param  string $type
	 * @return void
	 */
	public function clearConditions($type = null)
	{
		$cart = $this->items();

		if ($type)
		{
			foreach ($cart->conditions as $key => $value)
			{
				if ($value['type'] === $type)
				{
					unset($cart->conditions[$key]);
				}
			}
		}
		else
		{
			$cart->conditions = array();
		}

		$this->updateCart($cart);
	}

	/**
	 * Returns the total cart weight.
	 *
	 * @return float
	 */
	public function weight()
	{
		return $this->items()->reduce(function($result, $item)
		{
			return $result += $item->weight();
		});
	}

	/**
	 * Search for items with the given criteria.
	 *
	 * @param  array  $data
	 * @return array
	 */
	public function find($data)
	{
		$rows = array();

		foreach ($this->items() as $item)
		{
			if ($item->find($data))
			{
				$rows[] = $item;
			}
		}

		return $rows;
	}

	/**
	 * Returns the list of required indexes.
	 *
	 * @return array
	 */
	public function getRequiredIndexes()
	{
		return $this->requiredIndexes;
	}

	/**
	 * Sets the required indexes.
	 *
	 * By default we will merge the provided indexes with the current
	 * indexes, you can change this behavior by setting the second
	 * parameter as false.
	 *
	 * @param  array  $indexes
	 * @param  bool   $merge
	 * @return void
	 */
	public function setRequiredIndexes($indexes = array(), $merge = true)
	{
		$currentIndexes = $merge ? $this->getrequiredIndexes() : array();

		$reservedIndexes = $this->reservedIndexes;

		$this->requiredIndexes = array_unique(array_merge($currentIndexes, (array) $indexes, $reservedIndexes));
	}

	/**
	 * Returns the storage driver.
	 *
	 * @return mixed
	 */
	public function getStorage()
	{
		return $this->storage;
	}

	/**
	 * Sets the storage driver.
	 *
	 * @param  \Cartalyst\Cart\Storage\StorageInterface  $storage
	 * @return void
	 */
	public function setStorage(StorageInterface $storage)
	{
		$this->storage = $storage;
	}

	/**
	 * Check if an item exists in the cart.
	 *
	 * @param  string  $rowId
	 * @return bool
	 */
	protected function itemExists($rowId)
	{
		return $this->items()->has($rowId);
	}

	/**
	 * Updates the cart.
	 *
	 * @param  \Cartalyst\Cart\Collections\CartCollection
	 * @return void
	 */
	protected function updateCart($cart = null)
	{
		$this->storage->put($cart);
	}

	/**
	 * Returns all the applied and valid cart conditions.
	 *
	 * @return array
	 */
	protected function cartConditions()
	{
		return $this->items()->conditions;
	}

	/**
	 * Prepare the attributes.
	 *
	 * @param  array  $attributes
	 * @return \Cartalyst\Cart\Collections\ItemAttributesCollection
	 * @throws \Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException
	 */
	protected function prepareAttributes(array $attributes)
	{
		// Create a new attributes collection for this item
		$attributesCollection = new ItemAttributesCollection;

		// Store each option on the collection
		foreach ($attributes as $index => $option)
		{
			if (empty($option['value']))
			{
				throw new CartMissingRequiredIndexException('value');
			}

			$attributesCollection->put($index, new ItemCollection($option));
		}

		return $attributesCollection;
	}

	/**
	 * Generate a unique identifier based on the item data.
	 *
	 * @param  string  $id
	 * @param  array   $attributes
	 * @return string
	 */
	protected function generateRowId($id, $attributes)
	{
		return md5($id.serialize($attributes));
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
