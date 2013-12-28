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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Support\Collection;

class BaseCollection extends Collection {

	/**
	 * Holds all tax values.
	 *
	 * @var float
	 */
	protected $tax;

	/**
	 * Holds all discount values.
	 *
	 * @var float
	 */
	protected $discount;

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
	 * Return the tax value of the item.
	 *
	 * @return float
	 */
	public function getTax()
	{
		return $this->tax;
	}

	/**
	 * Set the tax value of the item.
	 *
	 * @param  float  $tax
	 * @return void
	 */
	public function setTax($tax)
	{
		$this->tax = $tax;
	}

	/**
	 * Return the discount value.
	 *
	 * @return float
	 */
	public function getDiscount()
	{
		return $this->discount;
	}

	/**
	 * Set the discount value.
	 *
	 * @param  float  $discount
	 * @return void
	 */
	public function setDiscount($discount)
	{
		$this->discount = $discount;
	}

	/**
	 * Return total.
	 *
	 * @return float
	 */
	public function total()
	{
		$this->applyConditions();

		return $this->subtotal ?: $this->subtotal($this->price);
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
	public function conditions()
	{
		return $this->conditions;
	}

	/**
	 * Clear conditions.
	 */
	public function clearConditions()
	{
		$this->conditions = array();
	}

	/**
	 * Apply all conditions
	 *
	 * @return void
	 */
	protected function applyConditions()
	{
		// Reset price
		$this->price = $this->get('price');

		// Reset subtotal
		$this->subtotal = $this->subtotal();

		// Run price conditions first
		$this->applyTypeConditions('price', 'discount');
		$this->applyTypeConditions('price');
		$this->applyTypeConditions('price', 'tax');

		// Run subtotal conditions
		$this->applyTypeConditions('subtotal', 'discount');
		$this->applyTypeConditions('subtotal');
		$this->applyTypeConditions('subtotal', 'tax');
	}

	/**
	 * Apply specific conditions
	 *
	 * @param  float   $target
	 * @param  string  $type
	 * @return void
	 */
	public function applyTypeConditions($target, $type = null)
	{

		foreach ($this->conditions() as $condition)
		{

			if ($condition->get('type') === $type)
			{
				if ($condition->get('target') === $target)
				{
					// Temporarily store subtotal
					$tempSubtotal = $this->subtotal;

					if ($target === 'price')
					{
						$value = $target === 'price' ? $this->getPrice() : 0;
					}
					else if ($target === 'subtotal')
					{
						$value = $target === 'subtotal' ? $this->subtotal ? $this->subtotal : $this->subtotal($this->price) : $this->subtotal($this->price);
					}

					// Apply condition
					$price = $condition->apply($this, $value);

					if ($target === 'price')
					{
						// Update price
						$this->setPrice($price);

						// Update subtotal with the new price
						$this->subtotal = $this->subtotal($price);
					}
					else if ($target === 'subtotal')
					{
						// Update subtotal
						$this->subtotal = $price;
					}

					// Store taxes
					if ($condition->get('type') === 'tax')
					{
						$this->setTax($this->getTax() + $this->subtotal - $tempSubtotal);
					}

					// Store discounts
					if ($condition->get('type') === 'discount')
					{
						$this->setDiscount($this->getDiscount() + $this->subtotal - $tempSubtotal);
					}

				}
			}

		}

	}

	/**
	 * Return applied taxes.
	 *
	 * @return float
	 */
	public function tax()
	{
		$this->setTax(0);

		$this->applyConditions();

		return $this->getTax();
	}

	/**
	 * Return applied discounts.
	 *
	 * @return float
	 */
	public function discount()
	{
		$this->setDiscount(0);

		$this->applyConditions();

		return $this->getDiscount();
	}

}
