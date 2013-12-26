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

class CartCollection extends BaseCollection {

	/**
	 * Return the cart subtotal.
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
	 * Return the sum of all item taxes.
	 *
	 * @param  \Cartalyst\Conditions\Condition  $tax
	 * @return float
	 */
	public function itemTaxes()
	{
		$total = 0;

		foreach ($this->items() as $item)
		{
			$item->applyConditions();
			$total += $item->tax();
		}

		return $total;
	}

}
