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
	 * Magic method.
	 *
	 * @param  string  $method
	 * @return mixed
	 */
	public function __get($method)
	{
		if (method_exists($this, $method))
		{
			return $this->{$method}();
		}

		if ($this->has($method))
		{
			return $this->get($method);
		}

		return null;
	}

}
