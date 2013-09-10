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
use Cartalyst\Cart\Collections\ItemAttributesCollection;
use Cartalyst\Cart\Collections\ItemCollection;
use Cartalyst\Conditions\Condition;
use Cartalyst\Cart\Exceptions\CartInvalidAttributesException;
use Cartalyst\Cart\Exceptions\CartInvalidPriceException;
use Cartalyst\Cart\Exceptions\CartInvalidQuantityException;
use Cartalyst\Cart\Exceptions\CartItemNotFoundException;
use Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException;
use Cartalyst\Cart\Storage\StorageInterface;
use Cartalyst\Cart\Weight;
use Cartalyst\Tax\Tax;

class Cart {

	/**
	 * The storage driver used by Cart.
	 *
	 * @var Cartalyst\Cart\Storage\StorageInterface
	 */
	protected $storage;

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
	 * Holds all the cart conditions.
	 *
	 * @var array
	 */
	protected $conditions = array();

	/**
	 * Constructor.
	 *
	 * @param  \Cartalyst\Cart\Storage\StorageInterface  $storage
	 * @param  \Cartalyst\Tax\Tax  $tax
	 * @param  \Cartalyst\Cart\Weight  $weight
	 * @return void
	 */
	public function __construct(StorageInterface $storage = null, Tax $tax, Weight $weight)
	{
		$this->storage = $storage;
		$this->tax = $tax;
		$this->weight = $weight;
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

		// Make sure the quantity is a number, and remove any leading zeros,
		// and make sure the value is rounded.
		$quantity = (float) round($item['quantity']);

		// Check if the quantity value is correct
		if ( ! is_numeric($quantity) or $quantity < 1)
		{
			throw new CartInvalidQuantityException;
		}

		// Remove any leading zeros and anything that isn't a number or a
		// decimal point from the price.
		$price = (float) $item['price'];

		// Check if the price value is correct
		if ( ! is_numeric($price))
		{
			throw new CartInvalidPriceException;
		}

		// Get this item attributes
		$attributes = ! empty($item['attributes']) ? $item['attributes'] : array();

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
			$row = new ItemCollection(array_merge($item, array(
				'rowId'      => $rowId,
				'quantity'   => $quantity,
				'price'      => $price,
				'attributes' => $attributesCollection,
			)));
		}

		// Update the item subtotal
		$row->put('subtotal', (float) $row->quantity * $row->price);

		// Get the cart contents
		$cart = $this->items();

		// Add the item to the cart
		$cart->put($rowId, $row);

		// Update the cart contents
		$this->updateCart($cart);

		return $cart;
	}

	/**
	 * Remove an item or items from the cart.
	 *
	 * @param  mixed
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
				$this->remove($rowId);
			}
		}
		else
		{
			foreach ($items as $rowId)
			{
				// Check if the item exists
				if ( ! $this->itemExists($rowId))
				{
					throw new CartItemNotFoundException;
				}

				// Get the cart contents
				$cart = $this->items();

				// Remove the item from the cart
				$cart->forget($rowId);
			}

			$this->updateCart($cart);
		}

		return true;
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

		return $cart;
	}

	/**
	 * Empties the cart.
	 *
	 * @return void
	 */
	public function clear()
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
	 * Return the cart total.
	 *
	 * @return float
	 */
	public function total()
	{
		return $this->subTotal() + $this->tax();
	}

	/**
	 * Return the cart subtotal.
	 *
	 * @return float
	 */
	public function subtotal()
	{
		$total = 0;

		foreach ($this->items() as $item)
		{
			$total += $item->subtotal;
		}

		/*
		foreach ($this->conditions() as $condition)
		{
			if ($condition->get('valid'))
			{
				$actions = $condition->get('actions', array());

				foreach ($actions as $action)
				{
					if ($action['target'] === 'subtotal')
					{
						$total = $this->calculate($action, $total);
					}
				}
			}
		}
		/**/

		return (float) $total;
	}

	/**
	 * Return the total items on the cart.
	 *
	 * @return int
	 */
	public function quantity()
	{
		$total = 0;

		foreach ($this->items() as $item)
		{
			$total += $item->quantity;
		}

		return (int) $total;
	}

	/**
	 * Return the sum of all item taxes.
	 *
	 * @param  \Cartalyst\Conditions\Condition  $tax
	 * @return float
	 */
	public function tax(Condition $tax = null)
	{
		$total = 0;

		if (is_null($tax))
		{
			foreach ($this->taxRates() as $rate)
			{
				$total += $this->tax($rate);
			}

			return $total;
		}

		foreach ($this->items() as $item)
		{
			$rate = $item->get('condition');

			if ( ! is_null($rate) and $tax->get('name') === $rate->get('name'))
			{
				$total += $item->subtotal();
			}
		}

		return $this->tax->setRate($tax->get('value'))->setValue($total)->charged();
	}

	/**
	 * Return all the applied tax rates both global and per item taxes.
	 *
	 * @return array
	 */
	public function taxRates()
	{
		$rates = array();

		// Per item taxes
		foreach ($this->items() as $item)
		{
			if ($condition = $item->get('condition'))
			{
				$rates[$condition->get('name')] = $condition;
			}
		}

		// Global taxes
		foreach ($this->conditions() as $condition)
		{
			if ($condition->get('type') === 'tax')
			{
				$rates[$condition->get('name')] = $condition;
			}
		}

		return $rates;
	}

	/**
	 * Return all the applied discounts.
	 *
	 * @return array
	 */
	public function discounts()
	{
		$discounts = array();

		foreach ($this->conditions() as $condition)
		{
			if ($condition->get('type') === 'discount')
			{
				$discounts[$condition->get('name')] = $condition;
			}
		}

		return $discounts;
	}

	/**
	 * Return the total cart weight.
	 *
	 * @return float
	 */
	public function weight()
	{
		$total = 0;

		foreach ($this->items() as $item)
		{
			$total += $item->weight();
		}

		return (float) $total;
	}

	/**
	 * Return the cart contents.
	 *
	 * @return \Cartalyst\Cart\Collections\CartCollection
	 */
	public function items()
	{
		// Get all the items
		$items = $this->storage->has() ? $this->storage->get() : new CartCollection;

		// Apply actions to the items
		foreach ($this->conditions() as $condition)
		{
			$rules = $condition->get('rules', array());

			$actions = $condition->get('actions', array());

			foreach ($rules as $rule)
			{
				foreach ($items as $item)
				{
					if ($item->get('name') === $rule->get('value'))
					{
						# @TODO: check if the condition is a discount or something else?!
						# and move this logic elsewhere
						foreach ($actions as $action)
						{
							$target = $action->get('target');

							if (strpos($target, 'item-') !== 'false')
							{
								// Prepare the target
								$target = str_replace('item-', '', $target);

								// Only apply the action if the condition is valid
								if ($condition->get('valid'))
								{
									$discount = $item->{$target} - $this->calculate($action, $item->{$target});

									$this->discounts[$condition->get('name')] = $discount;

									$item->put('discounted', $discount);
								}
								else
								{
									$item->forget('discounted');
								}
							}
						}
					}
				}
			}
		}

		// Return the items
		return $items;
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
			$currentInstance = $this->identify();

			$this->instance($instance);
		}

		$rows = array();

		foreach ($this->items() as $item)
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
	public function identify()
	{
		return $this->storage->identify();
	}

	/**
	 * Return all the cart instances.
	 *
	 * @return array
	 */
	public function instances()
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
	 * @param  string  $instance
	 * @return bool
	 */
	public function destroy($instance = null)
	{
		if ( ! is_null($instance))
		{
			$this->instance($instance);
		}

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
		return $this->items()->has($rowId);
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




##############################################


	/**
	 * Set a condition.
	 *
	 * @param  Cartalyst\Conditions\Condition  $condition
	 * @return void
	 */
	public function condition(Condition $condition)
	{
		$condition->validate($this);

		$this->conditions[] = $condition;
	}

	/**
	 * Return all the applied and valid conditions.
	 *
	 * @return array
	 */
	public function conditions()
	{
		$conditions = array();

		foreach ($this->conditions as $condition)
		{
			$conditions[$condition->get('name')] = $condition;
		}

		return $conditions;
	}

	/**
	 * Return all the conditions that were applied only to items.
	 *
	 * @return array
	 */
	public function itemConditions()
	{
		$conditions = array();

		foreach ($this->items() as $item)
		{
			if ($condition = $item->get('condition'))
			{
				$conditions[$condition->get('name')] = $condition;
			}
		}

		return $conditions;
	}


	# rethink this...
	protected function calculate($rule, $value)
	{
		$operation = $rule->get('operation');
		$operator = $rule->get('operator');

		if ($operation === 'percentage')
		{
			#
			return $value - ($value * $rule->get('value'));
		}


		switch ($operator)
		{
			case 'sum':
			default:

				return $value + $rule->get('value');

				break;

			case 'multiply':

				return $value * $rule->get('value');

				break;

			case 'subtract':
			case 'sub':

				return $value - $rule->get('value');

				break;
		}
	}


	public function discountValue($condition)
	{
		return $this->discounts[$condition->get('name')];
	}
}
