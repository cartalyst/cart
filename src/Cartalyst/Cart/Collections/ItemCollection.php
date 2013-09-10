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

class ItemCollection extends BaseCollection {

	/**
	 * Return the item subtotal, taking into consideration
	 * the item attributes prices.
	 *
	 * @return float
	 */
	public function subtotal()
	{
		if ($this->has('subtotal.discounted'))
		{
			return $this->get('subtotal.discounted');
		}

		if ($this->has('subtotal'))
		{
			return $this->get('subtotal');
		}

		$attributesTotal = $this->get('attributes')->total;

		return (float) ($this->get('price') + $attributesTotal) * $this->get('quantity');
	}

	/**
	 * Return the tax value of the item.
	 *
	 * @return float
	 */
	public function tax()
	{
		return (float) $this->get('condition')->get('value');
	}

	/**
	 * Return the total weight of the item.
	 *
	 * @return float
	 */
	public function weight()
	{
		return (float) $this->get('weight') * $this->get('quantity');
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
					return $this->attributes->{$key}->find($val);
				}
			}

			return $this->{$key} === $value ? true : false;
		}
	}

}
