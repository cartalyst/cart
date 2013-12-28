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

class ItemAttributesCollection extends Collection {

	/**
	 * Return the total price of all the options together.
	 *
	 * @return float
	 */
	public function getTotal()
	{
		$total = 0;

		foreach ($this as $option)
		{
			$total += $option->get('price');
		}

		return (float) $total;
	}

}
