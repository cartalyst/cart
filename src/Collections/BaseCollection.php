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

use Illuminate\Support\Collection;

class BaseCollection extends Collection {

	/**
	 * Holds all conditions.
	 *
	 * @var array
	 */
	protected $conditions = array();

	/**
	 * Holds conditions results.
	 *
	 * @var array
	 */
	protected $conditionResults = array();

	/**
	 * Holds conditions results grouped by name.
	 *
	 * @var array
	 */
	protected $totalConditionResults = array();

	/**
	 * Holds the order in which conditions apply.
	 *
	 * @var array
	 */
	protected $conditionsOrder = array(
		'discount',
		'other',
		'tax',
	);

	/**
	 * Holds the item price.
	 *
	 * @var float
	 */
	protected $price;

	/**
	 * Holds the item subtotal.
	 *
	 * @var int
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
	 * Set the item price.
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
	 * Set the conditions order.
	 *
	 * @param  array  $order
	 * @return void
	 */
	public function setConditionsOrder($order)
	{
		$this->conditionsOrder = $order;
	}

	/**
	 * Sets a new condition.
	 *
	 * @param  \Cartalyst\Conditions\Condition  $condition
	 * @return void
	 */
	public function condition($condition)
	{
		if (empty($condition)) return;

		if (is_array($condition))
		{
			foreach ($condition as $c)
			{
				$this->condition($c);
			}

			return;
		}

		if ($condition->validate($this))
		{
			$this->conditions[] = $condition;
		}

		$this->conditionResults[$condition->get('type')]['price'] = 0;

		$this->conditionResults[$condition->get('type')]['subtotal'] = 0;
	}

	/**
	 * Returns all the applied and valid conditions.
	 *
	 * @return array
	 */
	public function conditions()
	{
		return $this->conditions;
	}

	/**
	 * Clear the conditions.
	 *
	 * @return void
	 */
	public function clearConditions()
	{
		$this->conditions = array();
	}

	/**
	 * Apply specific conditions.
	 *
	 * @return void
	 */
	public function applySpecificConditions($type = null, $target = 'subtotal')
	{
		// Reset the subtotal
		$this->subtotal = $this->subtotal();

		// Reset the price
		$this->setPrice($this->get('price'));

		foreach ($this->conditionsOrder as $key)
		{
			$this->conditionResults[$key]['price'] = 0;

			$this->conditionResults[$key]['price'] = 0;

			$this->conditionResults[$key]['subtotal'] = 0;

			$this->conditionResults[$key]['subtotal'] = 0;
		}

		// Apply price conditions
		foreach ($this->conditionsOrder as $conditionType)
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
		foreach ($this->conditionsOrder as $conditionType)
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
	 * @param  integer $value
	 * @return float
	 */
	public function applyCondition($type, $target = 'subtotal', $value = 0)
	{
		$subtotal = 0;

		foreach ($this->conditionsOfType($type) as $condition)
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
						$this->totalConditionResults[$condition->get('type')] = array();
					}

					$this->totalConditionResults[$condition->get('type')][$condition->get('name')] = $condition->result();
				}

				$subtotal += $condition->result();
			}
		}

		return $subtotal;
	}

	/**
	 * Returns the applied discounts total.
	 *
	 * When passing a boolean true as the second parameter,
	 * it will include the items discounts as well.
	 *
	 * @param  bool  $includeItems
	 * @return float
	 */
	public function discountsTotal($includeItems = true)
	{
		$this->applySpecificConditions();

		$total = 0;

		if ($includeItems)
		{
			foreach ($this->items() as $item)
			{
				$item->applySpecificConditions();

				$total += $item->conditionResults['discount']['price'] + $item->conditionResults['discount']['subtotal'];
			}

			$total += $this->conditionResults['discount']['price'] + $this->conditionResults['discount']['subtotal'];
		}
		else
		{
			$total += $this->conditionResults['discount']['price'] + $this->conditionResults['discount']['subtotal'];
		}

		return $total;

		$total = $this->applySpecificConditions('discount') - $this->applySpecificConditions(null, 'price');

		if ($includeItems)
		{
			$total += $this->itemsDiscountsTotal();
		}

		return $total;
	}

	/**
	 * Returns all the applied taxes total.
	 *
	 * When passing a boolean true as the second parameter,
	 * it will include the items discounts as well.
	 *
	 * @param  bool  $includeItems
	 * @return float
	 */
	public function taxesTotal($includeItems = true)
	{
		$this->applySpecificConditions();

		$total = 0;

		if ($includeItems)
		{
			foreach ($this->items() as $item)
			{
				$item->applySpecificConditions();

				$total += $item->conditionResults['tax']['price'] + $item->conditionResults['tax']['subtotal'];
			}

			$total += $this->conditionResults['tax']['price'] + $this->conditionResults['tax']['subtotal'];
		}
		else
		{
			$total += $this->conditionResults['tax']['price'] + $this->conditionResults['tax']['subtotal'];
		}

		return $total;

		$total = $this->applySpecificConditions('tax') - $this->applySpecificConditions(null, 'price');

		if ($includeItems)
		{
			$total += $this->itemsDiscountsTotal();
		}

		return $total;
	}

	/**
	 * Returns the cart total.
	 *
	 * @return float
	 */
	public function total()
	{
		return $this->applySpecificConditions();
	}

	/**
	 * Return conditions by type.
	 *
	 * @return array
	 */
	protected function conditionsOfType($type = null)
	{
		$conditions = array();

		foreach ($this->conditions as $condition)
		{
			if ($condition->get('type') === $type)
			{
				$conditions[] = $condition;
			}
		}

		return $conditions;
	}

}
