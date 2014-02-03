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

class CartCollection extends BaseCollection {

	/**
	 * Return the items subtotal with conditions applied.
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
	 * Return the items subtotal without conditions.
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
	 * Return the total number of items in the cart.
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
	 * Returns all the conditions sum grouped by type.
	 *
	 * When passing a boolean true as the second parameter,
	 * it will include the items discounts as well.
	 *
	 * @param  string  $type
	 * @param  bool    $includeItems
	 * @return array
	 */
	public function conditionsTotal($type = null, $includeItems = true)
	{
		$this->totalConditionResults = array();

		$this->applySpecificConditions();

		if ($includeItems)
		{
			foreach ($this->items() as $item)
			{
				$item->applySpecificConditions();

				$this->totalConditionResults = array_merge_recursive(
					$item->getConditionResults(),
					$this->totalConditionResults
				);
			}
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

		if (isset($this->totalConditionResults[$type]))
		{
			return $this->totalConditionResults[$type];
		}

		return $this->totalConditionResults;
	}

	/**
	 * Return sum of item conditions.
	 *
	 * @param  string $type
	 * @return float
	 */
	public function itemsConditionsTotalSum($type = null)
	{
		if ( ! $type)
		{
			return array_sum(array_map(function($item) {
			    return is_array($item) ? array_sum($item) : $item;
			}, $this->itemsConditionsTotal()));
		}

		return array_sum($this->itemsConditionsTotal($type));
	}

	/**
	 * Return sum of conditions.
	 *
	 * @param  string $type
	 * @return float
	 */
	public function conditionsTotalSum($type = null)
	{
		if ( ! $type)
		{
			return array_sum(array_map(function($item) {
			    return is_array($item) ? array_sum($item) : $item;
			}, $this->conditionsTotal()));
		}

		return array_sum($this->conditionsTotal($type));
	}

	/**
	 * Return conditions by type.
	 *
	 * @return array
	 */
	protected function conditionsOfType($type = null)
	{
		$conditions = array();

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
		$conditions = array();

		foreach ($this->items() as $item)
		{
			if ($_conditions = $item->get('conditions'))
			{
				foreach ($_conditions as $condition)
				{
					$conditions[] = $condition;
				}
			}
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
		$this->totalConditionResults = array();

		foreach ($this->items() as $item)
		{
			$item->applySpecificConditions();

			$this->totalConditionResults = array_merge_recursive(
				$item->getConditionResults(),
				$this->totalConditionResults
			);
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

		if (isset($this->totalConditionResults[$type]))
		{
			return $this->totalConditionResults[$type];
		}

		return $this->totalConditionResults;
	}

	/**
	 * Returns all the applied discounts from all the items.
	 *
	 * @return array
	 */
	public function itemsDiscounts()
	{
		$discounts = array();

		foreach ($this->items() as $item)
		{
			$discounts[] = $item->discounts();
		}

		return $discounts;
	}

	/**
	 * Returns the sum of all item discounts.
	 *
	 * @return float
	 */
	public function itemsDiscountsTotal()
	{
		return $this->items()->sum(function($item)
		{
			return $item->discountsTotal(false);
		});
	}

	/**
	 * Returns all the applied tax rates from all the items.
	 *
	 * @return array
	 */
	public function itemsTaxes()
	{
		$taxes = array();

		foreach ($this->items() as $item)
		{
			$taxes[] = $item->taxes();
		}

		return $taxes;
	}

	/**
	 * Returns the sum of all item taxes.
	 *
	 * @return float
	 */
	public function itemsTaxesTotal()
	{
		return $this->items()->sum(function($item)
		{
			return $item->taxesTotal(false);
		});
	}

}
