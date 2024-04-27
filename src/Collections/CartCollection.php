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
 * @version    8.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2024, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Cartalyst\Cart\Collections;

use Serializable;
use Cartalyst\Cart\Cart;
use Illuminate\Support\Arr;
use Cartalyst\Cart\Exceptions\CartInvalidPriceException;
use Cartalyst\Cart\Exceptions\CartItemNotFoundException;
use Cartalyst\Cart\Exceptions\CartInvalidQuantityException;
use Cartalyst\Cart\Exceptions\CartInvalidAttributesException;
use Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException;

class CartCollection extends BaseCollection implements Serializable
{
    /**
     * The properties that should be serialized.
     *
     * @var array
     */
    protected $serializable = [
        'items',
        'metaData',
        'conditions',
        'conditionsOrder',
        'requiredIndexes',
        'itemsConditionsOrder',
    ];

    /**
     * Holds the order in which conditions apply.
     *
     * @var array
     */
    protected $conditionsOrder = [
        'discount',
        'other',
        'tax',
    ];

    /**
     * Holds the order in which items conditions apply.
     *
     * @var array
     */
    protected $itemsConditionsOrder = [
        'discount',
        'other',
        'tax',
    ];

    /**
     * Holds all the required indexes.
     *
     * @var array
     */
    protected $requiredIndexes = [
        'id',
        'name',
    ];

    /**
     * Holds all the reserved indexes.
     *
     * @var array
     */
    protected $reservedIndexes = [
        'price',
        'quantity',
    ];

    /**
     * Holds the meta data.
     *
     * @var array
     */
    protected $metaData = [];

    /**
     * The cart instance.
     *
     * @var \Cartalyst\Cart\Cart
     */
    protected $cart;

    /**
     * Adds a single or multiple items to the cart.
     *
     * @param array $item
     *
     * @throws \Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException
     * @throws \Cartalyst\Cart\Exceptions\CartInvalidQuantityException
     * @throws \Cartalyst\Cart\Exceptions\CartInvalidPriceException
     * @throws \Cartalyst\Cart\Exceptions\CartInvalidAttributesException
     *
     * @return mixed
     */
    public function add($item)
    {
        // Do we have multiple items?
        if ($this->isMulti($item)) {
            $items = [];

            foreach ($item as $_item) {
                $items[] = $this->add($_item);
            }

            return $items;
        }

        // Validate the required indexes
        foreach ($this->getRequiredIndexes() as $index) {
            if (! array_key_exists($index, $item)) {
                throw new CartMissingRequiredIndexException($index);
            }
        }

        // Make sure the quantity is an integer
        $quantity = $item['quantity'];

        // Check if the quantity value is correct
        if (! is_numeric($quantity) || $quantity < 1) {
            throw new CartInvalidQuantityException();
        }

        // Make sure we have a proper price value
        $price = $item['price'];

        if (! is_numeric($price)) {
            throw new CartInvalidPriceException();
        }

        $price = (float) $price;

        // Make sure we have proper and valid item attributes
        $attributes = Arr::get($item, 'attributes', []);

        if (! is_array($attributes)) {
            throw new CartInvalidAttributesException();
        }

        // Generate the unique row id
        $rowId = $this->generateRowId($item['id'], Arr::except($item, ['price', 'quantity']));

        // Pull item conditions
        $conditions = Arr::pull($item, 'conditions', []);

        // Check if the item already exists on the cart
        if ($this->exists($rowId)) {
            // Get the item
            $row = $this->item($rowId);

            // Update the item quantity
            $row->put('quantity', $row->get('quantity') + $quantity);
        } else {
            // Prepare the attributes
            $attributes = $this->prepareItemAttributes($attributes);

            // Create a new item
            $row = new ItemCollection(array_merge($item, compact('rowId', 'quantity', 'price', 'attributes')));
        }

        // Assign item conditions
        $row->condition($conditions);

        // Set items conditions order
        $row->setConditionsOrder($this->getItemsConditionsOrder());

        // Set the item price
        $row->setPrice($price);

        // Add the item to the cart
        $this->put($rowId, $row);

        // Fire the 'cartalyst.cart.added' event
        $this->cart->fire('added', [$this->item($rowId), $this->cart]);

        return $row;
    }

    /**
     * Removes a single or multiple items from the cart.
     *
     * @param mixed $items
     *
     * @throws \Cartalyst\Cart\Exceptions\CartItemNotFoundException
     *
     * @return bool
     */
    public function remove($items)
    {
        foreach ((array) $items as $rowId) {
            // Check if the item exists
            if (! $this->exists($rowId)) {
                throw new CartItemNotFoundException();
            }

            // Get the item information
            $item = $this->item($rowId);

            // Remove the item from the cart
            $this->forget($rowId);

            // Fire the 'cartalyst.cart.removed' event
            $this->cart->fire('removed', [$item, $this->cart]);
        }

        return true;
    }

    /**
     * Updates a single or multiple items that are on the cart.
     *
     * @param string $rowId
     * @param array  $data
     *
     * @throws \Cartalyst\Cart\Exceptions\CartItemNotFoundException
     *
     * @return mixed
     */
    public function update($rowId, $data = null)
    {
        // Do we have an array of items to be updated?
        if (is_array($rowId)) {
            foreach ($rowId as $item => $data) {
                $this->update($item, $data);
            }

            return true;
        }

        // Check if the item exists
        if (! $this->exists($rowId)) {
            throw new CartItemNotFoundException();
        }

        // Get the item we want to update
        $row = $this->get($rowId);

        // Pull item conditions
        $conditions = Arr::pull($data, 'conditions', $row->conditions());

        // Do we have multiple item data?
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($key === 'price') {
                    $value = (float) $value;
                } elseif ($key === 'quantity') {
                    $value = $value;
                } elseif ($key === 'attributes') {
                    $value = $this->prepareItemAttributes($value);
                }

                $row->put($key, $value);
            }
        }

        // We are probably updating the item quantity
        else {
            $row->put('quantity', $data);
        }

        // Remove the item if the quantity is less than one
        if ($row->get('quantity') < 1) {
            $this->remove($rowId);
        } else {
            // Reset the item conditions
            $row->removeConditions();

            // Assign conditions to the item
            $row->condition($conditions);

            // Set the item price
            $row->setPrice($row->get('price'));

            // Update the item
            $this->put($rowId, $row);

            // Fire the 'cartalyst.cart.updated' event
            $this->cart->fire('updated', [$this->item($rowId), $this->cart]);
        }

        return $row;
    }

    /**
     * Check if the item exists in the cart.
     *
     * @param string $rowId
     *
     * @return bool
     */
    public function exists($rowId)
    {
        return $this->has($rowId);
    }

    /**
     * Returns information about the provided item.
     *
     * @param string $rowId
     *
     * @throws \Cartalyst\Cart\Exceptions\CartItemNotFoundException
     *
     * @return \Cartalyst\Cart\Collections\ItemCollection
     */
    public function item($rowId)
    {
        // Check if the item exists
        if (! $this->exists($rowId)) {
            throw new CartItemNotFoundException();
        }

        // Return the item
        return $this->get($rowId);
    }

    /**
     * Returns the meta data.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getMetaData($key = null, $default = null)
    {
        return Arr::get($this->metaData, $key, $default);
    }

    /**
     * Sets meta data.
     *
     * @param string $key
     * @param mixed  $data
     *
     * @return void
     */
    public function setMetaData($key, $data)
    {
        Arr::set($this->metaData, $key, $data);

        // Fire the 'cartalyst.cart.metadata.added' event
        $this->cart->fire('metadata.added', [[$key => $data], $this->cart]);
    }

    /**
     * Removes the meta data.
     *
     * @param string $key
     *
     * @return void
     */
    public function removeMetaData($key = null)
    {
        if (! $key) {
            $this->metaData = [];
        } else {
            Arr::forget($this->metaData, $key);
        }

        // Fire the 'cartalyst.cart.metadata.removed' event
        $this->cart->fire('metadata.removed', [$key, $this->cart]);
    }

    /**
     * Returns the items subtotal with conditions applied.
     *
     * @return float
     */
    public function subtotal()
    {
        return $this->sum('total');
    }

    /**
     * Returns the items subtotal without conditions.
     *
     * @return float
     */
    public function itemsSubtotal()
    {
        return $this->sum('subtotal');
    }

    /**
     * Returns the total number of items in the cart.
     *
     * @return int
     */
    public function quantity()
    {
        return $this->sum('quantity');
    }

    /**
     * Returns the total cart weight.
     *
     * @return float
     */
    public function weight()
    {
        return $this->sum('weight');
    }

    /**
     * Returns the conditions by type.
     *
     * @param string $type
     * @param bool   $includeItems
     *
     * @return array
     */
    public function conditions($type = null, $includeItems = true)
    {
        $conditions = [];

        if (! $type) {
            if ($includeItems) {
                foreach ($this->items as $item) {
                    $conditions = array_merge($conditions, $item->conditions());
                }
            }

            return array_merge($conditions, $this->conditions);
        }

        foreach ($this->conditions as $condition) {
            if ($condition->get('type') === $type) {
                $conditions[] = $condition;
            }
        }

        return $conditions;
    }

    /**
     * Returns all the conditions that were applied only to items.
     *
     * @return array
     */
    public function itemsConditions()
    {
        $conditions = [];

        foreach ($this->items as $item) {
            $conditions = array_merge($conditions, $item->conditions());
        }

        return $conditions;
    }

    /**
     * Returns the items conditions total.
     *
     * @param string $type
     *
     * @return float
     */
    public function itemsConditionsTotal($type = null)
    {
        $this->conditionResults = [];

        foreach ($this->items as $item) {
            if (! $item->conditionResults()) {
                $item->total();
            }

            $this->conditionResults = array_merge_recursive(
                $item->conditionResults(),
                $this->conditionResults
            );
        }

        if ($type && empty($this->conditionResults[$type])) {
            return [];
        }

        foreach ($this->conditionResults as $key => $result) {
            foreach ($result as $name => $value) {
                if (is_array($value)) {
                    $this->conditionResults[$key][$name] = array_sum($value);
                }
            }
        }

        return Arr::get($this->conditionResults, $type, $this->conditionResults);
    }

    /**
     * Returns the sum of item conditions.
     *
     * @param string $type
     *
     * @return float
     */
    public function itemsConditionsTotalSum($type = null)
    {
        if (! $type) {
            return array_sum(array_map(function ($item) {
                return is_array($item) ? array_sum($item) : $item;
            }, $this->itemsConditionsTotal()));
        }

        return array_sum($this->itemsConditionsTotal($type));
    }

    /**
     * Returns the items conditions order.
     *
     * @return array
     */
    public function getItemsConditionsOrder()
    {
        return $this->itemsConditionsOrder;
    }

    /**
     * Sets the items conditions order.
     *
     * @param array $order
     *
     * @return void
     */
    public function setItemsConditionsOrder(array $order)
    {
        $this->itemsConditionsOrder = $order;
    }

    /**
     * Removes a condition by its name.
     *
     * @param string $name
     * @param bool   $includeItems
     *
     * @return void
     */
    public function removeConditionByName($name, $includeItems = true)
    {
        $this->removeConditions($name, $includeItems, 'name');
    }

    /**
     * Removes a condition by its type.
     *
     * @param string $type
     * @param bool   $includeItems
     *
     * @return void
     */
    public function removeConditionByType($type, $includeItems = true)
    {
        $this->removeConditions($type, $includeItems, 'type');
    }

    /**
     * Removes conditions.
     *
     * @param string $id
     * @param bool   $includeItems
     * @param string $target
     *
     * @return void
     */
    public function removeConditions($id = null, $includeItems = true, $target = 'type')
    {
        if ($id) {
            foreach ($this->conditions as $key => $value) {
                if (! isset($value[$target])) {
                    continue;
                }

                if ($value[$target] === $id) {
                    unset($this->conditions[$key]);
                }
            }
        } else {
            $this->conditions = [];
        }

        if ($includeItems) {
            foreach ($this->items as $item) {
                $item->removeConditions($id, false, $target);
            }
        }
    }

    /**
     * Search for items with the given criteria.
     *
     * @param array $data
     *
     * @return array
     */
    public function find($data)
    {
        $rows = [];

        foreach ($this->items as $item) {
            if ($item->find($data)) {
                $rows[] = $item;
            }
        }

        return $rows;
    }

    /**
     * Returns the list of required indexes.
     *
     * @return array
     */
    public function getRequiredIndexes()
    {
        return array_unique(array_merge($this->reservedIndexes, $this->requiredIndexes));
    }

    /**
     * Sets the required indexes.
     *
     * @param array $indexes
     * @param bool  $merge
     *
     * @return void
     */
    public function setRequiredIndexes($indexes = [], $merge = true)
    {
        $currentIndexes = $merge ? $this->requiredIndexes : [];

        $this->requiredIndexes = array_unique(array_merge($currentIndexes, (array) $indexes));
    }

    /**
     * Returns the cart instance.
     *
     * @return \Cartalyst\Cart\Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Sets the cart instance.
     *
     * @param \Cartalyst\Cart\Cart $cart
     *
     * @return $this
     */
    public function setCart(Cart $cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Serialize.
     *
     * @return string
     */
    public function serialize()
    {
        $data = [];

        foreach ($this->serializable as $key) {
            $data[$key] = $this->{$key};
        }

        return serialize($data);
    }

    /**
     * Serialize.
     *
     * @return array
     */
    public function __serialize(): array
    {
        $data = [];

        foreach ($this->serializable as $key) {
            $data[$key] = $this->{$key};
        }

        return $data;
    }

    /**
     * Unserialize.
     *
     * @return void
     */
    public function __unserialize(array $data): void
    {
        foreach ($this->serializable as $key) {
            $this->{$key} = $data[$key];
        }
    }

    /**
     * Unserialize.
     *
     * @param string $data
     *
     * @return void
     */
    public function unserialize($data)
    {
        $data = unserialize($data);

        foreach ($this->serializable as $key) {
            $this->{$key} = $data[$key];
        }
    }

    /**
     * Returns the properties that should be serialized.
     *
     * @return array
     */
    public function getSerializable()
    {
        return $this->serializable;
    }

    /**
     * Sets the properties that should be serialized.
     *
     * @param array $properties
     *
     * @return $this
     */
    public function setSerializable(array $properties)
    {
        $this->serializable = $properties;

        return $this;
    }

    /**
     * Prepares the item attributes.
     *
     * @param array $attributes
     *
     * @throws \Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException
     *
     * @return \Cartalyst\Cart\Collections\ItemAttributesCollection
     */
    protected function prepareItemAttributes(array $attributes)
    {
        $data = [];

        foreach ($attributes as $index => $option) {
            if (! isset($option['value'])) {
                throw new CartMissingRequiredIndexException('value');
            }

            $data[$index] = new ItemCollection($option);
        }

        return new ItemAttributesCollection($data);
    }

    /**
     * Generates a unique identifier based on the item data.
     *
     * @param mixed $id
     * @param array $item
     *
     * @return string
     */
    protected function generateRowId($id, $item)
    {
        return md5($id.serialize($item));
    }

    /**
     * Checks if the given array is a multidimensional array.
     *
     * @param array $array
     *
     * @return bool
     */
    protected function isMulti($array)
    {
        return is_array(array_shift($array));
    }
}
