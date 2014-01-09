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
	 * Holds all the conditions.
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
	 * Set's a new condition.
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
	 * Returns all the applied discounts.
	 *
	 * When passing a boolean true as the second parameter,
	 * it will include the items discounts as well.
	 *
	 * @param  bool  $includeItems
	 * @return array
	 */
	public function discounts($includeItems = true)
	{
		$discounts = array();

		foreach ($this->conditionsOfType('discount') as $condition)
		{
			$discounts[] = $condition;
		}

		if ($includeItems)
		{
			foreach ($this->items() as $item)
			{
				foreach ($item->conditionsOfType('discount') as $condition)
				{
					$discounts[] = $condition;
				}
			}
		}

		return $discounts;
	}

	/**
	 * Returns the applied discounts subtotal.
	 *
	 * @return float
	 */
	public function discountsSubtotal()
	{
		$subtotal = 0;

		foreach ($this->conditionsOfType('discount') as $condition)
		{
			if ($condition->get('target') === 'subtotal')
			{
				$condition->apply($this, $this->subtotal);

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
		$total = $this->conditionTotals('discount');

		if ($includeItems)
		{
			$total += $this->itemsDiscountsTotal();
		}

		return $total;
	}

	/**
	 * Return total.
	 *
	 * @return float
	 * @todo Rephrase the docblock description.
	 */
	public function discountedSubtotal()
	{
		$this->applyPriceConditions();

		return $this->subtotal + $this->discountsSubtotal();
	}

	/**
	 * Returns all the applied taxes.
	 *
	 * When passing a boolean true as the second parameter,
	 * it will include the items discounts as well.
	 *
	 * @param  bool  $includeItems
	 * @return array
	 */
	public function taxes($includeItems = true)
	{
		$taxes = array();

		foreach ($this->conditionsOfType('tax') as $condition)
		{
			$taxes[] = $condition;
		}

		if ($includeItems)
		{
			foreach ($this->items() as $item)
			{
				foreach ($item->conditionsOfType('tax') as $condition)
				{
					$taxes[] = $condition;
				}
			}
		}

		return $taxes;
	}

	/**
	 * Returns the tax applied that were applied on the subtotal.
	 *
	 * @return float
	 */
	public function taxesSubtotal()
	{
		$subtotal = 0;

		foreach ($this->conditionsOfType('tax') as $condition)
		{
			if ($condition->get('target') === 'subtotal')
			{
				$condition->apply($this, $this->discountOtherSubtotal());

				$subtotal += $condition->result();
			}
		}

		return $subtotal;
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
		$total = $this->conditionTotals('tax', $this->discountOtherSubtotal());

		if ($includeItems)
		{
			$total += $this->itemsTaxesTotal();
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
		return $this->discountedSubtotal() + $this->otherSubtotal() + $this->taxesSubtotal();
	}

	/**
	 * Returns all the conditions sum grouped by type.
	 *
	 * When passing a boolean true as the second parameter,
	 * it will include the items discounts as well.
	 *
	 * @param  bool  $includeItems
	 * @return array
	 */
	public function getConditionsTotal($type = null, $includeItems = true)
	{
		$rates = array();

		if ($includeItems)
		{
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
		}

		foreach($this->conditionsOfType($type) as $condition)
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

		return $rates;
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

		foreach ($this->conditionsOfType($type) as $condition)
		{
			if ($condition->get('target') === 'price')
			{
				$res = $condition->result() * $this->get('quantity');
			}
		}

		foreach ($this->conditionsOfType($type) as $condition)
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
		foreach ($this->conditionsOfType($type) as $condition)
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
	 * Return applied other conditions total.
	 *
	 * @return float
	 */
	protected function otherSubtotal()
	{
		$res = 0;

		foreach ($this->conditionsOfType() as $condition)
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
