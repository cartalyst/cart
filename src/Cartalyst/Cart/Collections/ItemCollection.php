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

use Cartalyst\Conditions\Condition;

class ItemCollection extends BaseCollection {

	/**
	 * Returns the item subtotal and it will take into
	 * consideration the attributes total.
	 *
	 * @param  float  $price
	 * @return float
	 */
	public function subtotal($price = null)
	{
		$price = $price ?: $this->get('price');

		$attributesTotal = $this->get('attributes')->getTotal();

		$total = $this->get('quantity') * ($price + $attributesTotal);

		return $total;
	}

	/**
	 * Search for items with the given criteria.
	 *
	 * @param  array  $data
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
	 * Returns the total item weight.
	 *
	 * @return float
	 */
	public function weight()
	{
		return (float) $this->get('weight') * $this->get('quantity');
	}

}
