<?php

/*
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
 * @version    4.0.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Cartalyst\Cart\Collections;

use Cartalyst\Conditions\Condition;

class ItemCollection extends BaseCollection
{
    /**
     * Sets the item price.
     *
     * @param float $price
     *
     * @return void
     */
    public function setPrice($price)
    {
        $this->put('price', (float) $price);
    }

    /**
     * Returns this item attributes.
     *
     * @return \Cartalyst\Collections\Collection
     */
    public function attributes()
    {
        return $this->get('attributes');
    }

    /**
     * Returns this item price.
     *
     * @param bool $withAttributes
     *
     * @return float
     */
    public function price($withAttributes = false)
    {
        $price = $this->get('price');

        if ($withAttributes) {
            $price += $this->attributes()->getTotal();
        }

        return $price;
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
     * @param float $price
     *
     * @return float
     */
    public function subtotal($price = null)
    {
        if ($price !== 0.00) {
            $price = $price ?: $this->price();
        }

        $attributesTotal = $this->attributes()->getTotal();

        return $this->quantity() * ($price + $attributesTotal);
    }

    /**
     * Returns the total item weight.
     *
     * @return float
     */
    public function weight()
    {
        $attributeWeights = $this->get('attributes')->sum(function ($option) {
            return $option->get('weight');
        });

        return ($this->get('weight') + $attributeWeights) * $this->quantity();
    }

    /**
     * Search for items with the given criteria.
     *
     * @param array $data
     *
     * @return bool
     */
    public function find($data)
    {
        $valid = true;

        foreach ($data as $key => $value) {
            if ($key === 'attributes') {
                foreach ($value as $key => $val) {
                    if ($attribute = $this->attributes()->get($key)) {
                        $valid = $valid && $attribute->find($val);
                    } else {
                        return false;
                    }
                }

                return $valid;
            }

            if ($key === 'price' || $key == 'weight') {
                $value = (float) $value;
            } elseif ($key === 'quantity') {
                $value = $value;
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
     * {@inheritdoc}
     */
    public function conditionsTotal($type = null, $includeItems = true)
    {
        return parent::conditionsTotal($type, false);
    }
}
