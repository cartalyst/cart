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
	 * Returns this item attributes.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function attributes()
	{
		return $this->get('attributes');
	}

	/**
	 * Returns this item price.
	 *
	 * @return float
	 */
	public function price()
	{
		return $this->get('price');
	}

	/**
	 * Returns this item quantity.
	 *
	 * @return int
	 */
	public function quantity()
	{
		return $this->get('quantity');
	}

	/**
	 * Returns this item subtotal and it will take into
	 * consideration the attributes total.
	 *
	 * @param  float  $price
	 * @return float
	 */
	public function subtotal($price = null)
	{
		$price = $price ?: $this->price();

		$attributesTotal = $this->attributes()->getTotal();

		$total = $this->quantity() * ($price + $attributesTotal);

		return $total;
	}

	/**
	 * Returns the total item weight.
	 *
	 * @return float
	 */
	public function weight()
	{
		return $this->get('weight') * $this->quantity();
	}

	/**
	 * Search for items with the given criteria.
	 *
	 * @param  array  $data
	 * @return bool
	 */
	public function find($data)
	{
		$valid = true;

		foreach ($data as $key => $value)
		{
			if ($key === 'attributes')
			{
				foreach ($value as $key => $val)
				{
					if ($attribute = $this->attributes()->get($key))
					{
						$valid = $valid && $attribute->find($val);
					}
					else
					{
						return false;
					}
				}

				return $valid;
			}

			if ($key === 'price')
			{
				$value = (float) $value;
			}

			if ($key === 'quantity')
			{
				$value = (int) $value;
			}

			if ($key === 'weight')
			{
				$value = (float) $value;
			}

			$valid = $valid && $this->get($key) === $value;
		}

		return $valid;
	}

	/**
	 * Returns the condition results grouped by name.
	 *
	 * @return array
	 */
	public function conditionResults()
	{
		return $this->conditionResults;
	}

	/**
	 * {@inheritDoc}
	 */
	public function conditionsTotal($type = null, $includeItems = true)
	{
		return parent::conditionsTotal($type, false);
	}

}
