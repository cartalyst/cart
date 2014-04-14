<?php namespace Cartalyst\Cart\Collections;
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

use Cartalyst\Cart\Cart;
use Illuminate\Support\Collection;

abstract class BaseCollection extends Collection {

	/**
	 * Holds all conditions.
	 *
	 * @var array
	 */
	protected $conditions = [];

	/**
	 * Holds conditions results.
	 *
	 * @var array
	 */
	protected $conditionResults = [];

	/**
	 * Holds the item price.
	 *
	 * @var float
	 */
	protected $price;

	/**
	 * Holds the item subtotal.
	 *
	 * @var float
	 */
	protected $subtotal;

	/**
	 * Returns the conditions order.
	 *
	 * @return array
	 */
	public function getConditionsOrder()
	{
		return $this->conditionsOrder;
	}

	/**
	 * Sets the conditions order.
	 *
	 * @param  array  $order
	 * @return void
	 */
	public function setConditionsOrder(array $order)
	{
		$this->conditionsOrder = $order;
	}

	/**
	 * Sets a new condition.
	 *
	 * @param  mixed  $condition
	 * @return void
	 */
	public function condition($condition)
	{
		$base = $this instanceof Cart ? $this->items() : $this;

		if (empty($condition)) return;

		if (is_array($condition))
		{
			foreach ($condition as $c)
			{
				$base->condition($c);
			}

			return;
		}

		if ($condition->validate($base))
		{
			$base->conditions[$condition->get('name')] = $condition;
		}

		if ($this instanceof Cart)
		{
			$this->updateCart($base);
		}
	}

	/**
	 * Clear the conditions.
	 *
	 * @param  string  $type
	 * @return void
	 */
	public function clearConditions($type = null)
	{
		if ($type)
		{
			foreach ($this->conditions as $key => $value)
			{
				if ($value['type'] === $type)
				{
					unset($this->conditions[$key]);
				}
			}
		}
		else
		{
			$this->conditions = [];
		}
	}

	/**
	 * Returns the total.
	 *
	 * @param  string  $type
	 * @return float
	 */
	public function total($conditionType = null)
	{
		$this->conditionResults = [];

		$this->subtotal = $this->subtotal();

		$this->price = $this->get('price');

		// Price conditions
		foreach ($this->getConditionsOrder() as $type)
		{
			$this->price += $this->applyCondition($type, 'price', $this->price);
		}

		$this->subtotal = $this->subtotal($this->price);

		// Subtotal conditions
		foreach ($this->getConditionsOrder() as $type)
		{
			$this->subtotal += $this->applyCondition($type, 'subtotal', $this->subtotal);

			if ($conditionType === $type)
			{
				break;
			}
		}

		return $this->subtotal;
	}

	/**
	 * Apply a condition.
	 *
	 * @param  string  $conditionType
	 * @param  string  $target
	 * @param  int  $value
	 * @return float
	 */
	public function applyCondition($conditionType, $target = 'subtotal', $value = 0)
	{
		$subtotal = 0;

		foreach ($this->conditions($conditionType) as $condition)
		{
			if ($condition->get('target') === $target)
			{
				$condition->apply($this, $value);

				$name   = $condition->get('name');
				$type   = $condition->get('type');
				$result = $condition->result();

				if ( ! isset($this->conditionResults[$type]))
				{
					$this->conditionResults[$type] = [];
				}

				if ($target === 'price')
				{
					$this->conditionResults[$type][$name] = $result * $this->get('quantity');
				}
				else
				{
					$this->conditionResults[$type][$name] = $result;
				}

				// Add exclusive conditions only
				if ( ! head($condition->get('actions'))->get('inclusive'))
				{
					$subtotal += $result;
				}
			}
		}

		return $subtotal;
	}

	/**
	 * Returns all the conditions sum grouped by type.
	 *
	 * When passing a boolean true as the second parameter,
	 * it will include the items discounts as well.
	 *
	 * @param  string  $type
	 * @param  bool  $includeItems
	 * @return array
	 */
	public function conditionsTotal($type = null, $includeItems = true)
	{
		$this->conditionResults = [];

		$this->total();

		if ($includeItems)
		{
			foreach ($this->items() as $item)
			{
				$this->conditionResults = array_merge_recursive(
					$item->conditionResults(),
					$this->conditionResults
				);
			}
		}

		if ($type && ! isset($this->conditionResults[$type]))
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
	 * Returns the sum of conditions.
	 *
	 * @param  string  $type
	 * @param  bool  $includeItems
	 * @return float
	 */
	public function conditionsTotalSum($type = null, $includeItems = true)
	{
		if ( ! $type)
		{
			return array_sum(array_map(function($item)
			{
			    return is_array($item) ? array_sum($item) : $item;
			}, $this->conditionsTotal($type, $includeItems)));
		}

		return array_sum($this->conditionsTotal($type, $includeItems));
	}

	/**
	 * Returns all the conditions with the given type.
	 *
	 * @param  string  $type
	 * @return array
	 */
	public function conditions($type = null)
	{
		$conditions = [];

		foreach ($this->conditions as $condition)
		{
			if ($type)
			{
				if ($type === $condition->get('type'))
				{
					$conditions[] = $condition;
				}
			}
			else
			{
				$conditions[] = $condition;
			}
		}

		return $conditions;
	}

}
