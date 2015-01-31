<?php

/**
 * Part of the Cart package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Cart
 * @version    1.0.8
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Cart\Collections;

use Illuminate\Support\Collection;

class ItemAttributesCollection extends Collection
{
    /**
     * Returns the total price of all the attributes together.
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->sum(function ($option) {
            return $option->get('price');
        });
    }
}
