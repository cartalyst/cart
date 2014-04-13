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
	 * Holds conditions results grouped by name.
	 *
	 * @var array
	 */
	protected $totalConditionResults = [];

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
	 * Returns the item price.
	 *
	 * @return float
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * Sets the item price.
	 *
	 * @param  float  $price
	 * @return void
	 */
	public function setPrice($price)
	{
		$this->price = $price;
	}

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
	 * Returns the condition results grouped by name.
	 *
	 * @return array
	 */
	public function totalConditionResults()
	{
		return $this->totalConditionResults;
	}

	/**
	 * Apply conditions.
	 *
	 * @param  string  $type
	 * @return float
	 */
	public function applyConditions($type = null)
	{
		// Reset calculated conditions
		$this->totalConditionResults = [];

		// Reset the subtotal
		$this->subtotal = $this->subtotal();

		// Reset the price
		$this->setPrice($this->get('price'));

		foreach ($this->getConditionsOrder() as $key)
		{
			$this->conditionResults[$key]['price'] = 0;

			$this->conditionResults[$key]['subtotal'] = 0;
		}

		// Apply price conditions
		foreach ($this->getConditionsOrder() as $conditionType)
		{
			$oldPrice = $this->price;

			$this->price += $this->applyCondition($conditionType, 'price', $this->getPrice());

			if ($oldPrice != $this->price)
			{
				$this->conditionResults[$conditionType]['price'] = $this->subtotal($this->price) - $this->subtotal;
			}
		}

		$this->subtotal = $this->subtotal($this->price);

		// Apply subtotal conditions
		foreach ($this->getConditionsOrder() as $conditionType)
		{
			$this->conditionResults[$conditionType]['subtotal'] = $this->applyCondition($conditionType, 'subtotal', $this->subtotal);

			$this->subtotal += $this->applyCondition($conditionType, 'subtotal', $this->subtotal);

			if ($type === $conditionType)
			{
				break;
			}
		}

		return $this->subtotal;
	}

	/**
	 * Apply a condition.
	 *
	 * @param  string  $type
	 * @param  string  $target
	 * @param  int  $value
	 * @return float
	 */
	public function applyCondition($type, $target = 'subtotal', $value = 0)
	{
		$subtotal = 0;

		foreach ($this->conditions($type) as $condition)
		{
			if ($condition->get('target') === $target)
			{
				$condition->apply($this, $value);

				if (isset($this->totalConditionResults[$condition->get('type')][$condition->get('name')]))
				{
					$this->totalConditionResults[$condition->get('type')][$condition->get('name')] = $condition->result();
				}
				else
				{
					if ( ! isset($this->totalConditionResults[$condition->get('type')]))
					{
						$this->totalConditionResults[$condition->get('type')] = [];
					}

					if ($target === 'price')
					{
						$this->totalConditionResults[$condition->get('type')][$condition->get('name')] = $condition->result() * $this->get('quantity');
					}
					else
					{
						$this->totalConditionResults[$condition->get('type')][$condition->get('name')] = $condition->result();
					}
				}

				// Add exclusive conditions only
				if ( ! $inclusive = head($condition->get('actions'))->get('inclusive'))
				{
					$subtotal += $condition->result();
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
		$this->totalConditionResults = [];

		$this->applyConditions();

		if ($includeItems)
		{
			foreach ($this->items() as $item)
			{
				$item->applyConditions();

				$this->totalConditionResults = array_merge_recursive(
					$item->totalConditionResults(),
					$this->totalConditionResults
				);
			}
		}

		if ($type && ! isset($this->totalConditionResults[$type]))
		{
			return [];
		}

		foreach ($this->totalConditionResults as $key => $result)
		{
			foreach ($result as $name => $value)
			{
				if (is_array($value))
				{
					$this->totalConditionResults[$key][$name] = array_sum($value);
				}
			}
		}

		return array_get($this->totalConditionResults, $type, $this->totalConditionResults);
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
	 * Returns the cart total.
	 *
	 * @return float
	 */
	public function total()
	{
		return $this->applyConditions();
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
				if ($condition->get('type') === $type)
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
