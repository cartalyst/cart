<?php namespace Cartalyst\Cart;
/**
 * Part of the Cart package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Cart
 * @version    1.1.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
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
use Illuminate\Support\Collection;

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
	 * Flag for whether we should fire events or not.
	 *
	 * @var bool
	 */
	protected $fireEvents = true;

	/**
	 * Holds all the required indexes.
	 *
	 * @var array
	 */
	protected $requiredIndexes = [
		'id',
		'name',
	];

	/**
	 * Holds all the reserved indexes.
	 *
	 * @var array
	 */
	protected $reservedIndexes = [
		'price',
		'quantity',
	];

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
			$items = [];

			foreach ($item as $_item)
			{
				$items[] = $this->add($_item);
			}

			return $items;
		}

		// Validate the required indexes
		foreach ($this->getRequiredIndexes() as $index)
		{
			if (empty($item[$index]))
			{
				throw new CartMissingRequiredIndexException($index);
			}
		}

		// Make sure the quantity is an integer
		$quantity = (int) $item['quantity'];

		// Check if the quantity value is correct
		if ( ! is_numeric($quantity) || $quantity < 1)
		{
			throw new CartInvalidQuantityException;
		}

		// Make sure we have a proper price value
		$price = $item['price'];

		if ( ! is_numeric($price))
		{
			throw new CartInvalidPriceException;
		}

		$price = (float) $price;

		// Make sure we have proper and valid item attributes
		$attributes = array_get($item, 'attributes', []);

		if ( ! is_array($attributes))
		{
			throw new CartInvalidAttributesException;
		}

		// Generate the unique row id
		$rowId = $this->generateRowId($item['id'], array_except($item, ['price', 'quantity']));

		// Check if the item already exists on the cart
		if ($this->exists($rowId))
		{
			// Get the item
			$row = $this->item($rowId);

			// Update the item quantity
			$row->put('quantity', $row->get('quantity') + $quantity);
		}
		else
		{
			// Prepare the attributes
			$attributes = $this->prepareItemAttributes($attributes);

			// Create a new item
			$row = new ItemCollection(array_merge($item, compact('rowId', 'quantity', 'price', 'attributes')));
		}

		// Assign item conditions
		$row->condition(array_get($item, 'conditions', []));

		// Set items conditions order
		$row->setConditionsOrder($this->getItemsConditionsOrder());

		// Set the item price
		$row->setPrice($price);

		// Get the cart contents
		$cart = $this->items();

		// Add the item to the cart
		$cart->put($rowId, $row);

		// Update the cart contents
		$this->updateCart($cart);

		// Fire the 'cartalyst.cart.added' event
		$this->fire('added', [$this->item($rowId), $this]);

		return $row;
	}

	/**
	 * Removes a single or multiple items from the cart.
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
			if ( ! $this->exists($rowId))
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
			$this->fire('removed', [$item, $this]);
		}

		$this->updateCart($cart);

		return true;
	}

	/**
	 * Updates a single or multiple items that are on the cart.
	 *
	 * @param  string  $rowId
	 * @param  array  $data
	 * @return mixed
	 * @throws \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function update($rowId, $data = null)
	{
		// Do we have an array of items to be updated?
		if (is_array($rowId))
		{
			foreach ($rowId as $item => $data)
			{
				$this->update($item, $data);
			}

			return true;
		}

		// Check if the item exists
		if ( ! $this->exists($rowId))
		{
			throw new CartItemNotFoundException;
		}

		// Get the cart contents
		$cart = $this->items();

		// Get the item we want to update
		$row = $cart->get($rowId);

		// Do we have multiple item data?
		if (is_array($data))
		{
			foreach ($data as $key => $value)
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
					$value = $this->prepareItemAttributes($value);
				}

				$row->put($key, $value);
			}
		}

		// We are probably updating the item quantity
		else
		{
			$row->put('quantity', (int) $data);
		}

		// Remove the item if the quantity is less than one
		if ($row->get('quantity') < 1)
		{
			$this->remove($rowId);
		}
		else
		{
			// Reset the item conditions
			$row->removeConditions();

			// Assign conditions to the item
			$row->condition(array_get($row, 'conditions'));

			// Set the item price
			$row->setPrice($row->get('price'));

			// Update the item
			$cart->put($rowId, $row);

			// Fire the 'cartalyst.cart.updated' event
			$this->fire('updated', [$this->item($rowId), $this]);
		}

		return $row;
	}

	/**
	 * Check if the item exists in the cart.
	 *
	 * @param  string  $rowId
	 * @return bool
	 */
	public function exists($rowId)
	{
		return $this->items()->has($rowId);
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
		if ( ! $this->exists($rowId))
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
		if ($this->storage->has())
		{
			return $this->storage->get();
		}

		$this->updateCart($cart = new CartCollection);

		// Fire the 'cartalyst.cart.created' event
		$this->fire('created', $cart);

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
		$this->fire('cleared', $this);
	}

	/**
	 * Synchronizes a collection of data with the cart.
	 *
	 * @param  \Illuminate\Support\Collection  $items
	 * @return void
	 */
	public function sync(Collection $items)
	{
		// Turn events off
		$this->fireEvents = false;

		foreach ($items->all() as $item)
		{
			$this->add($item);
		}

		// Turn events on
		$this->fireEvents = true;
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
	 * Returns the items conditions order.
	 *
	 * @return array
	 */
	public function getItemsConditionsOrder()
	{
		return $this->items()->getItemsConditionsOrder();
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

		$cart->setItemsConditionsOrder($order);

		$this->updateCart($cart);
	}

	/**
	 * Removes a condition by its name.
	 *
	 * @param  string  $name
	 * @param  bool  $includeItems
	 * @return void
	 */
	public function removeConditionByName($name, $includeItems = true)
	{
		$this->removeConditions($name, $includeItems, 'name');
	}

	/**
	 * Removes a condition by its type.
	 *
	 * @param  string  $type
	 * @param  bool  $includeItems
	 * @return void
	 */
	public function removeConditionByType($type, $includeItems = true)
	{
		$this->removeConditions($type, $includeItems, 'type');
	}

	/**
	 * Removes conditions.
	 *
	 * @param  string  $id
	 * @param  bool  $includeItems
	 * @param  string  $target
	 * @return void
	 */
	public function removeConditions($id = null, $includeItems = true, $target = 'type')
	{
		$items = $this->items();

		if ($id)
		{
			foreach ($items->conditions as $key => $value)
			{
				if ($value[$target] === $id)
				{
					unset($items->conditions[$key]);
				}
			}
		}
		else
		{
			$items->conditions = [];
		}

		if ($includeItems)
		{
			foreach (array_keys($items->toArray()) as $key)
			{
				$items[$key]->removeConditions($id, false, $target);
			}
		}

		$this->updateCart($items);
	}

	/**
	 * Search for items with the given criteria.
	 *
	 * @param  array  $data
	 * @return array
	 */
	public function find($data)
	{
		$rows = [];

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
		return array_unique(array_merge($this->reservedIndexes, $this->requiredIndexes));
	}

	/**
	 * Sets the required indexes.
	 *
	 * @param  array  $indexes
	 * @param  bool  $merge
	 * @return void
	 */
	public function setRequiredIndexes($indexes = [], $merge = true)
	{
		$currentIndexes = $merge ? $this->requiredIndexes : [];

		$this->requiredIndexes = array_unique(array_merge($currentIndexes, (array) $indexes));
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
	 * Returns the event dispatcher instance.
	 *
	 * @return \Illuminate\Events\Dispatcher
	 */
	public function getDispatcher()
	{
		return $this->dispatcher;
	}

	/**
	 * Sets the event dispatcher instance.
	 *
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function setDispatcher(Dispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	/**
	 * Updates the cart.
	 *
	 * @param  \Cartalyst\Cart\Collections\CartCollection  $cart
	 * @return void
	 */
	protected function updateCart($cart = null)
	{
		$this->storage->put($cart);
	}

	/**
	 * Fires an event.
	 *
	 * @param  string  $event
	 * @param  mixed  $data
	 * @return void
	 */
	protected function fire($event, $data)
	{
		// Check if we should fire events
		if ($this->fireEvents)
		{
			$this->dispatcher->fire("cartalyst.cart.{$event}", $data);
		}
	}

	/**
	 * Prepares the item attributes.
	 *
	 * @param  array  $attributes
	 * @return \Cartalyst\Cart\Collections\ItemAttributesCollection
	 * @throws \Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException
	 */
	protected function prepareItemAttributes(array $attributes)
	{
		$data = [];

		foreach ($attributes as $index => $option)
		{
			if (empty($option['value']))
			{
				throw new CartMissingRequiredIndexException('value');
			}

			$data[$index] = new ItemCollection($option);
		}

		return new ItemAttributesCollection($data);
	}

	/**
	 * Generates a unique identifier based on the item data.
	 *
	 * @param  mixed  $id
	 * @param  array  $item
	 * @return string
	 */
	protected function generateRowId($id, $item)
	{
		return md5($id.serialize($item));
	}

	/**
	 * Checks if the given array is a multidimensional array.
	 *
	 * @param  array  $array
	 * @return bool
	 */
	protected function isMulti($array)
	{
		return is_array(array_shift($array));
	}

}
