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
		$subtotal = 0;

		foreach ($this->items() as $item)
		{
			$subtotal += $item->total();
		}

		return $subtotal;
	}

	/**
	 * Return the items subtotal without conditions.
	 *
	 * @return float
	 */
	public function itemsSubtotal()
	{
		$subtotal = 0;

		foreach ($this->items() as $item)
		{
			$subtotal += $item->subtotal();
		}

		return $subtotal;
	}

	/**
	 * Return the total number of items in the cart.
	 *
	 * @return int
	 */
	public function quantity()
	{
		$total = 0;

		foreach ($this->items() as $item)
		{
			$total += $item->get('quantity');
		}

		return (int) $total;
	}

	/**
	 * Returns the item conditions sum grouped by type.
	 *
	 * @return  array
	 */
	public function getItemsConditionsTotal($type = null)
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
	 * Return all the applied tax rates from all the items.
	 *
	 * @return array
	 */
	public function itemsTaxes()
	{
		$taxes = array();

		foreach ($this->items() as $item)
		{
			foreach ($item->conditions() as $condition)
			{
				if ($condition->get('type') === 'tax')
				{
					$taxes[] = $condition;
				}
			}
		}

		return $taxes;
	}

	/**
	 * Return the sum of all item taxes.
	 *
	 * @return float
	 */
	public function itemsTaxesTotal()
	{
		$total = 0;

		foreach ($this->items() as $item)
		{
			$total += $item->taxTotal(false);
		}

		return $total;
	}

	/**
	 * Return the sum of all item discounts.
	 *
	 * @return float
	 */
	public function itemsDiscountsTotal()
	{
		$total = 0;

		foreach ($this->items() as $item)
		{
			$total += $item->discountTotal(false);
		}

		return $total;
	}

}
