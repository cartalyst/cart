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
	 * Return the item price.
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
	 * Set a condition.
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
	}

	/**
	 * Return all the applied and valid conditions.
	 *
	 * @return array
	 */
	public function conditions($type = null)
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

	/**
	 * Clear conditions.
	 *
	 * @return void
	 */
	public function clearConditions()
	{
		$this->conditions = array();
	}

	/**
	 * Return all the applied discounts.
	 *
	 * @return array
	 */
	public function discounts()
	{
		$discounts = array();

		foreach ($this->conditions('discount') as $condition)
		{
			$discounts[] = $condition;
		}

		return $discounts;
	}

	/**
	 * Return all the applied taxes.
	 *
	 * @return array
	 */
	public function taxes()
	{
		$discounts = array();

		foreach ($this->conditions('tax') as $condition)
		{
			$discounts[] = $condition;
		}

		return $discounts;
	}

	/**
	 * Return total.
	 *
	 * @return float
	 */
	public function total()
	{
		return $this->discountedSubtotal() + $this->otherSubtotal() + $this->taxSubtotal();
	}

	/**
	 * Return the applied taxes total.
	 *
	 * @return float
	 */
	public function taxTotal($items = false)
	{
		$res = $this->conditionTotals('tax', $this->discountOtherSubtotal());

		if ($items)
		{
			$res += $this->itemsTaxesTotal();
		}

		return $res;
	}

	/**
	 * Return the applied discounts total.
	 *
	 * @return float
	 */
	public function discountTotal($items = false)
	{
		$res = $this->conditionTotals('discount');

		if ($items)
		{
			$res += $this->itemsDiscountsTotal();
		}

		return $res;
	}

	/**
	 * Calculate condition totals.
	 *
	 * @param  string $type
	 * @param  float  $value
	 * @return float
	 */
	protected function conditionTotals($type = null, $value = null)
	{
		$res = 0;

		$this->applyPriceConditions();

		$value = $value ?: $this->subtotal($this->getPrice());

		foreach ($this->conditions($type) as $condition)
		{
			if ($condition->get('target') === 'price')
			{
				$res = $condition->result() * $this->get('quantity');
			}
		}

		foreach ($this->conditions($type) as $condition)
		{
			if ($condition->get('target') === 'subtotal')
			{
				$condition->apply($this, $value);

				$res += $condition->result();
			}
		}

		return $res;
	}

	/**
	 * Return total.
	 *
	 * @return float
	 */
	protected function discountedSubtotal()
	{
		$this->applyPriceConditions();

		return $this->subtotal + $this->discountSubtotal();
	}

	/**
	 * Return the tax applied on subtotal.
	 *
	 * @return float
	 */
	protected function taxSubtotal()
	{
		$res = 0;

		foreach ($this->conditions('tax') as $condition)
		{
			if ($condition->get('target') === 'subtotal')
			{
				$condition->apply($this, $this->discountOtherSubtotal());

				$res += $condition->result();
			}
		}

		return $res;
	}

	/**
	 * Return subtotal after applying discounts and other conditions
	 *
	 * @return float
	 */
	protected function discountOtherSubtotal()
	{
		return $this->discountedSubtotal() + $this->otherSubtotal();
	}

	/**
	 * Apply price based conditions.
	 *
	 * @return void
	 */
	protected function applyPriceConditions()
	{
		// Reset the price
		$this->setPrice($this->get('price'));

		// Reset the subtotal
		$this->subtotal = $this->subtotal();

		// Run price conditions first
		$this->applyPriceTypeConditions('price', 'discount');
		$this->applyPriceTypeConditions('price');
		$this->applyPriceTypeConditions('price', 'tax');
	}

	/**
	 * Apply specific conditions
	 *
	 * @param  float   $target
	 * @param  string  $type
	 * @return void
	 */
	protected function applyPriceTypeConditions($target, $type = null)
	{
		foreach ($this->conditions($type) as $condition)
		{
			if ($condition->get('target') === $target)
			{
				if ($target === 'price')
				{
					$value = $this->getPrice() ?: $this->get('price');

					// Apply condition
					$price = $condition->apply($this, $value);

					// Update price
					$this->setPrice($price);

					// Update subtotal with the new price
					$this->subtotal = $this->subtotal($price);
				}

			}
		}
	}

	/**
	 * Return applied discounts total.
	 *
	 * @param  bool  $includeItems
	 * @return float
	 */
	protected function discountSubtotal()
	{
		$res = 0;

		foreach ($this->conditions('discount') as $condition)
		{
			if ($condition->get('target') === 'subtotal')
			{
				$condition->apply($this, $this->subtotal);

				$res += $condition->result();
			}
		}

		return $res;
	}

	/**
	 * Return applied other conditions total.
	 *
	 * @return float
	 */
	protected function otherSubtotal()
	{
		$res = 0;

		foreach ($this->conditions() as $condition)
		{
			if ($condition->get('target') === 'subtotal')
			{
				$condition->apply($this, $this->discountedSubtotal());

				$res += $condition->result();
			}
		}

		return $res;
	}

}
