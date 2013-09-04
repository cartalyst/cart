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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Cart\Collections\CartCollection;
use Cartalyst\Cart\Collections\ItemCollection;
use Cartalyst\Cart\Collections\ItemAttributesCollection;
use Cartalyst\Cart\Exceptions\CartInvalidAttributesException;
use Cartalyst\Cart\Exceptions\CartInvalidPriceException;
use Cartalyst\Cart\Exceptions\CartInvalidQuantityException;
use Cartalyst\Cart\Exceptions\CartItemNotFoundException;
use Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException;
use Cartalyst\Cart\Storage\StorageInterface;
use Illuminate\Events\Dispatcher;

class Cart {

	/**
	 * The storage driver used by Cart.
	 *
	 * @var Cartalyst\Cart\Storage\StorageInterface
	 */
	protected $storage;

	/**
	 * The event dispatcher instance.
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $events;

	/**
	 * Holds all the required indexes.
	 *
	 * @var array
	 */
	protected $requiredIndexes = array(
		'id',
		'name',
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
	 * Constructor.
	 *
	 * @param  \Cartalyst\Cart\Storage\StorageInterface  $storage
	 * @return void
	 */
	public function __construct(StorageInterface $storage = null, Dispatcher $events)
	{
		$this->events = $events;
		$this->storage = $storage;
	}

	/**
	 * Adds a single item or multiple items to the cart.
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
		$this->events->fire('cart.adding', array());

		// Do we have multiple items?
		if ($this->isMulti($item))
		{
			foreach ($item as $i)
			{
				$this->add($i);
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

		// Make sure the quantity is a number, and remove any leading zeros
		$quantity = (float) $item['quantity'];

		// Make sure that the quantity value is rounded
		$quantity = round($quantity);

		// Remove any leading zeros and anything that isn't a number or a
		// decimal point from the price.
		$price = (float) $item['price'];

		// Check if the quantity value is correct
		if ( ! is_numeric($quantity) or $quantity < 1)
		{
			throw new CartInvalidQuantityException;
		}

		// Check if the price value is correct
		if ( ! is_numeric($price))
		{
			throw new CartInvalidPriceException;
		}

		// Get this item attribute
		$attributes = ! empty($item['attributes']) ? $item['attributes'] : array();

		// Validate the attribute
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
			$row = $this->getItem($rowId);

			// Update the item quantity
			$row->put('quantity', $row->quantity + $quantity);
		}
		else
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

			// Create a new item
			$row = new ItemCollection(array(
				'rowId'      => $rowId,
				'id'         => $item['id'],
				'name'       => $item['name'],
				'quantity'   => $quantity,
				'price'      => $price,
				'tax'        => ! empty($item['tax']) ? $item['tax'] : null,
				'weight'     => ! empty($item['weight']) ? $item['weight'] : null,
				'attributes' => $attributesCollection,
			));
		}

		// Update the item subtotal
		$row->put('subtotal', (float) $row->quantity * $row->price);

		// Get the cart contents
		$cart = $this->getContent();

		// Add the item to the cart
		$cart->put($rowId, $row);

		// Update the cart contents
		$this->updateCart($cart);

		$this->events->fire('cart.added', array($cart));

		return $this->storage->getHeaders();
	}

	/**
	 * Remove an item or items from the cart.
	 *
	 * @return bool
	 * @throws \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function remove()
	{
		$items = func_get_args();

		if ($this->isMulti($items))
		{
			foreach ($items[0] as $rowId)
			{
				$this->removeItem($rowId);
			}
		}
		else
		{
			foreach ($items as $rowId)
			{
				$this->removeItem($rowId);
			}
		}

		return true;
	}

	/**
	 * Remove an item from the cart.
	 *
	 * @param  string  $rowId
	 * @return bool
	 * @throws \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	protected function removeItem($rowId)
	{
		$this->events->fire('cart.removing', array());

		// Do we have an array of items to be removed?
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

		$this->events->fire('cart.removed', array($cart));

		// Update the cart contents
		return $this->updateCart($cart);
	}

	/**
	 * Updates an item that is on the cart.
	 *
	 * @param  string  $rowId
	 * @param  array   $attributes
	 * @return bool
	 * @throws \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function update($rowId, $attributes = null)
	{
		$this->events->fire('cart.updating', array());

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
		$cart = $this->getContent();

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
					$value = (float) round($value);
				}

				$row->put($key, $value);
			}
		}

		// We are probably updating the quantity
		else
		{
			// Make sure the quantity is a number, and remove any leading zeros
			$quantity = (float) $attributes;

			// Make sure that the quantity value is rounded
			$quantity = round($quantity);

			$row->put('quantity', $quantity);
		}

		// Update the item subtotal
		$row->put('subtotal', (float) $row->quantity * $row->price);

		// If quantity is less than one, we remove the item
		if ($row->quantity < 1)
		{
			$cart->forget($rowId);
		}
		else
		{
			$cart->put($rowId, $row);
		}

		$this->events->fire('cart.updated', array($cart));

		return $cart;
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
	 * @return \Cartalyst\Cart\Collections\ItemCollection
	 * @throws \Cartalyst\Cart\Exceptions\CartItemNotFoundException
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

	/**
	 * Return the cart total.
	 *
	 * @return float
	 */
	public function getTotal()
	{
		$total = 0;

		foreach ($this->getContent() as $item)
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
		$total = 0;

		foreach ($this->getContent() as $item)
		{
			$total += $item->quantity;
		}

		return (int) $total;
	}

	/**
	 * Return the sum of all item taxes.
	 *
	 * @return float
	 */
	public function getTaxTotal()
	{
		$total = 0;

		foreach ($this->getContent() as $item)
		{
			$total += $item->getTax();
		}

		return (float) $total;
	}

	/**
	 * Return the total cart weight.
	 *
	 * @return float
	 */
	public function getWeightTotal()
	{
		$total = 0;

		foreach ($this->getContent() as $item)
		{
			$total += $item->getWeight();
		}

		return (float) $total;
	}

	/**
	 * Return the cart contents.
	 *
	 * @return \Cartalyst\Cart\Collections\CartCollection
	 */
	public function getContent()
	{
		return $this->storage->has() ? $this->storage->get() : new CartCollection;
	}

	/**
	 * Search for items with the given criteria.
	 *
	 * @param  array   $data
	 * @param  string  $instance
	 * @return array
	 */
	public function find($data, $instance = null)
	{
		if ( ! is_null($instance))
		{
			$currentInstance = $this->getInstance();

			$this->instance($instance);
		}

		$rows = array();

		foreach ($this->getContent() as $item)
		{
			if ($item->find($data))
			{
				$rows[] = $item;
			}
		}

		if ( ! is_null($instance))
		{
			$this->instance($currentInstance);
		}

		return $rows;
	}

	/**
	 * Return the current cart instance.
	 *
	 * @return string
	 */
	public function getInstance()
	{
		return $this->storage->getInstance();
	}

	/**
	 * Return all the cart instances.
	 *
	 * @return array
	 */
	public function getInstances()
	{
		return $this->storage->instances() ?: array();
	}

	/**
	 * Change the cart instance.
	 *
	 * @return \Cartalyst\Cart\Cart
	 */
	public function instance($instance)
	{
		$this->storage->setInstance($instance);

		return $this;
	}

	/**
	 * Remove the cart instance.
	 *
	 * @return bool
	 */
	public function forgetInstance()
	{
		$this->storage->forget();

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
	 * Updates the cart.
	 *
	 * @param  \Cartalyst\Cart\Collections\CartCollection
	 * @return void
	 */
	protected function updateCart($cart)
	{
		$this->storage->put($cart);
	}

	/**
	 * Return the list of required indexes.
	 *
	 * @return array
	 */
	public function getRequiredIndexes()
	{
		return $this->requiredIndexes;
	}

	/**
	 * Set required indexes.
	 *
	 * By default we will merge the provided indexes with the current
	 * indexes, you can change this behavior by setting the second
	 * parameter as false.
	 *
	 * @param  bool  $merge
	 * @return void
	 */
	public function setRequiredIndexes($indexes = array(), $merge = true)
	{
		$indexes = (array) $indexes;

		$currentIndexes = $merge ? $this->getrequiredIndexes() : array();

		$reservedIndexes = $this->reservedIndexes;

		$this->requiredIndexes = array_unique(array_merge($currentIndexes, $indexes, $reservedIndexes));
	}

	/**
	 * Return the session key.
	 *
	 * @return string
	 */
	public function getSessionKey()
	{
		return $this->storage->getKey();
	}

	/**
	 * Set the storage driver.
	 *
	 * @param  \Cartalyst\Cart\Storage\StorageInterface  $storage
	 * @return void
	 */
	public function setStorage(StorageInterface $storage)
	{
		$this->storage = $storage;
	}

	/**
	 * Get the event dispatcher instance.
	 *
	 * @return \Illuminate\Events\Dispatcher
	 */
	public function getDispatcher()
	{
		return $this->events;
	}

	/**
	 * Set the event dispatcher instance.
	 *
	 * @param  \Illuminate\Events\Dispatcher
	 */
	public function setDispatcher(Dispatcher $events)
	{
		$this->events = $events;
	}

	/**
	 * Generate a unique identifier base on the item data.
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

	/**
	 * Validates if the provided arguments contains all the required indexes.
	 *
	 * @param  array  $arguments
	 * @return void
	 * @throws \Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException
	 */
	protected function validateIndexes($arguments)
	{
		foreach ($this->getRequiredIndexes() as $parameter)
		{
			if ( ! array_key_exists($parameter, $arguments))
			{
				throw new CartMissingRequiredIndexException($parameter);
			}
		}
	}





	/**
	 * Apply a discount to the cart.
	 *
	 * @param  \Cartalyst\Discounts\Models\Discount
	 * @return bool
	 */
	public function discount(/*Discount $discount*/)
	{
		echo 'todo';
	}

}
