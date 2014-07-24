<?php namespace Cartalyst\Cart\Collections;
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
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

class CartCollection extends BaseCollection {

	/**
	 * Holds the order in which conditions apply.
	 *
	 * @var array
	 */
	protected $conditionsOrder = [
		'discount',
		'other',
		'tax',
	];

	/**
	 * Holds the order in which items conditions apply.
	 *
	 * @var array
	 */
	protected $itemsConditionsOrder = [
		'discount',
		'other',
		'tax',
	];

	/**
	 * Holds the meta data.
	 *
	 * @var array
	 */
	protected $metaData = [];

	/**
	 * Returns the meta data.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function getMetaData($key = null)
	{
		return array_get($this->items()->metaData, $key);
	}

	/**
	 * Sets meta data.
	 *
	 * @param  array  $data
	 * @return void
	 */
	public function setMetaData($data)
	{
		$cart = $this->items();

		foreach ($data as $key => $value)
		{
			$value = array_merge(array_get($cart->metaData, $key, []), (array) $value);

			array_set($cart->metaData, $key, $value);
		}

		$this->updateCart($cart);
	}

	/**
	 * Removes the meta data.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function removeMetaData($key = null)
	{
		$cart = $this->items();

		if ( ! $key)
		{
			$cart->metaData = [];
		}
		else
		{
			array_forget($cart->metaData, $key);
		}

		$this->updateCart($cart);
	}

	/**
	 * Returns the items subtotal with conditions applied.
	 *
	 * @return float
	 */
	public function subtotal()
	{
		return $this->items()->sum(function($item)
		{
			return $item->total();
		});
	}

	/**
	 * Returns the items subtotal without conditions.
	 *
	 * @return float
	 */
	public function itemsSubtotal()
	{
		return $this->items()->sum(function($item)
		{
			return $item->subtotal();
		});
	}

	/**
	 * Returns the total number of items in the cart.
	 *
	 * @return int
	 */
	public function quantity()
	{
		return (int) $this->items()->sum(function($item)
		{
			return $item->get('quantity');
		});
	}

	/**
	 * Returns the total cart weight.
	 *
	 * @return float
	 */
	public function weight()
	{
		return $this->items()->sum(function($item)
		{
			return $item->weight();
		});
	}

	/**
	 * Returns the conditions by type.
	 *
	 * @param  string  $type
	 * @param  bool  $includeItems
	 * @return array
	 */
	public function conditions($type = null, $includeItems = true)
	{
		$conditions = [];

		if ( ! $type)
		{
			if ($includeItems)
			{
				foreach ($this->items() as $item)
				{
					$conditions = array_merge($conditions, $item->conditions());
				}
			}

			return array_merge($conditions, $this->items()->conditions);
		}

		foreach ($this->items()->conditions as $condition)
		{
			if ($condition->get('type') === $type)
			{
				$conditions[] = $condition;
			}
		}

		return $conditions;
	}

	/**
	 * Returns all the conditions that were applied only to items.
	 *
	 * @return array
	 */
	public function itemsConditions()
	{
		$conditions = [];

		foreach ($this->items() as $item)
		{
			$conditions = array_merge($conditions, $item->get('conditions'));
		}

		return $conditions;
	}

	/**
	 * Returns the items conditions total.
	 *
	 * @param  string  $type
	 * @return float
	 */
	public function itemsConditionsTotal($type = null)
	{
		$this->conditionResults = [];

		foreach ($this->items() as $item)
		{
			if ( ! $item->conditionResults())
			{
				$item->total();
			}

			$this->conditionResults = array_merge_recursive(
				$item->conditionResults(),
				$this->conditionResults
			);
		}

		if ($type && empty($this->conditionResults[$type]))
		{
			return [];
		}

		foreach ($this->conditionResults as $key => $result)
		{
			foreach ($result as $name => $value)
			{
				if (is_array($value))
				{
					$this->conditionResults[$key][$name] = array_sum($value);
				}
			}
		}

		return array_get($this->conditionResults, $type, $this->conditionResults);
	}

	/**
	 * Returns the sum of item conditions.
	 *
	 * @param  string  $type
	 * @return float
	 */
	public function itemsConditionsTotalSum($type = null)
	{
		if ( ! $type)
		{
			return array_sum(array_map(function($item)
			{
			    return is_array($item) ? array_sum($item) : $item;
			}, $this->itemsConditionsTotal()));
		}

		return array_sum($this->itemsConditionsTotal($type));
	}

	/**
	 * Returns the items conditions order.
	 *
	 * @return array
	 */
	public function getItemsConditionsOrder()
	{
		return $this->itemsConditionsOrder;
	}

	/**
	 * Sets the items conditions order.
	 *
	 * @param  array  $order
	 * @return void
	 */
	public function setItemsConditionsOrder(array $order)
	{
		$this->itemsConditionsOrder = $order;
	}

}
