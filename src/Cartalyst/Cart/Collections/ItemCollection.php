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

use Cartalyst\Conditions\Condition;

class ItemCollection extends BaseCollection {

	/**
	 * Return the item subtotal, taking into consideration
	 * the item attributes prices.
	 *
	 * @param  float  $price
	 * @return float
	 */
	public function subtotal($price = 0)
	{
		$price = $price ?: $this->get('price');

		$attributesPrice = $this->get('attributes')->getTotal();

		$total = $this->get('quantity') * ($price + $attributesPrice);

		return $total;
	}

	/**
	 * Search for items with the given criteria.
	 *
	 * @return bool
	 */
	public function find($data)
	{
		foreach ($data as $key => $value)
		{
			if ($key === 'attributes')
			{
				foreach ($value as $key => $val)
				{
					return $this->get('attributes')->get($key)->find($val);
				}
			}

			if ($key === 'price')
			{
				$value = (float) $value;
			}

			if ($key === 'quantity')
			{
				$value = (int) $value;
			}

			return $this->get($key) === $value;
		}
	}

	/**
	 * Return the total item weight.
	 *
	 * @return float
	 */
	public function weight()
	{
		return (float) $this->get('weight') * $this->get('quantity');
	}

	/**
	 * Return the applied taxes total.
	 *
	 * @return float
	 */
	public function taxTotal()
	{
		$this->setTax(0);

		$this->applyConditions();

		return $this->getTax();
	}

}
