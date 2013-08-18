<?php namespace Cartalyst\Cartify\Collections;
/**
 * Part of the Cartify package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Cartify
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Support\Collection;

class ItemCollection extends Collection {

	/**
	 * Magic method.
	 *
	 * @param  string  value
	 * @return mixed
	 */
	public function __get($value)
	{
		$method = "get".studly_case($value);

		if (method_exists($this, $method))
		{
			return $this->{$method}();
		}

		if ($this->has($value))
		{
			return $this->get($value);
		}

		return null;
	}

	/**
	 * Return the item subtotal, taking into consideration
	 * the item options prices.
	 *
	 * @return float
	 */
	public function getSubtotal()
	{
		$optionsTotal = $this->get('options')->total;

		return (float) ($this->get('price') + $optionsTotal) * $this->get('quantity');
	}

	/**
	 * Return the tax value of the item.
	 *
	 * @return float
	 */
	public function getTax()
	{
		return (float) $this->get('tax');
	}

	/**
	 *
	 *
	 * @return bool
	 */
	public function find($data)
	{
		foreach ($data as $key => $value)
		{
			if ($key === 'options')
			{
				return $this->{$key}->find($value);
			}

			return $this->{$key} == $value ? true : false;
		}
	}

}
