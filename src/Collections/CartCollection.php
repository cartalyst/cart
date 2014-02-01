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
	 * Returns the items conditions total grouped by type.
	 *
	 * @param  string  $type
	 * @return array
	 */
	public function itemsConditionsTotal($type = null)
	{
		$rates = array();

		foreach ($this->items() as $item)
		{
			foreach($item->conditionsOfType($type) as $condition)
			{
				$key = $condition->get('name');

				if (array_key_exists($key, $rates))
				{
					$rates[$key] += $condition->result();
				}
				else
				{
					$rates[$key] = $condition->result();
				}
			}
		}

		return $rates;
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
