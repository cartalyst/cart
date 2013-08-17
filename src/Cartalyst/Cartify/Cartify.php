<?php namespace Cartalyst\Cartify;
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

use Cartalyst\Cartify\Collections\CartCollection;
use Cartalyst\Cartify\Collections\ItemCollection;
use Cartalyst\Cartify\Collections\ItemOptionsCollection;
use Cartalyst\Cartify\Exceptions\CartInvalidDataException;
use Cartalyst\Cartify\Exceptions\CartInvalidPriceException;
use Cartalyst\Cartify\Exceptions\CartInvalidQuantityException;
use Cartalyst\Cartify\Exceptions\CartItemNotFoundException;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Session\Store as SessionStorage;

class Cartify {

	/**
	 * The session driver used by Cartify.
	 *
	 * @var \Illuminate\Session\Store
	 */
	protected $session;

	/**
	 * Cartify config repository.
	 *
	 * @var \Illuminate\Config\Repository
	 */
	protected $config;

	/**
	 * Cart instance.
	 *
	 * @var string
	 */
	protected $instance;

	/**
	 * Holds the session key.
	 *
	 * @var string
	 */
	protected $sessionKey;

	/**
	 * Holds all the required indexes.
	 *
	 * @var array
	 */
	protected $requiredIndexes = array(
		'id',
		'name',
		'price',
		'quantity',
	);

	/**
	 * Constructor.
	 *
	 * @param  \Illuminate\Session\Store  $session
	 * @return void
	 */
	public function __construct(SessionStorage $session = null, ConfigRepository $config)
	{
		// Store the session driver
		$this->session = $session;

		// Store the config repository
		$this->config = $config;

		// Set the default cart instance
		$this->instance($config->get('cartify::instance', 'main'));

		// Set the default session key
		$this->setSessionKey($config->get('cartify::session', 'cartify'));

		// Set the required indexes
		$this->setRequiredIndexes($config->get('cartify::requiredIndexes'));
	}

	/**
	 * Adds a new item to the cart.
	 *
	 * @param  array  $items
	 * @return mixed
	 * @throws Cartalyst\Cartify\Exceptions\CartInvalidDataException
	 * @throws Cartalyst\Cartify\Exceptions\CartInvalidQuantityException
	 * @throws Cartalyst\Cartify\Exceptions\CartInvalidPriceException
	 */
	public function add($item)
	{
		// Do we have multiple items?
		if ($this->isMulti($item))
		{
			foreach ($item as $i)
			{
				$this->add($i);
			}

			return true;
		}

		// Validate the required parameters
		foreach ($this->requiredIndexes as $parameter)
		{
			if (empty($item[$parameter]))
			{
				throw new CartInvalidDataException;
			}
		}

		// Make sure the quantity is a number, and remove any leading zeros
		$quantity = (float) $item['quantity'];

		// Remove any leading zeros and anything that isn't a number or a
		// decimal point from the price.
		$price = (float) $item['price'];

		// Check if the quantity value is correct
		if ( ! is_numeric($quantity) or $quantity == 0)
		{
			throw new CartInvalidQuantityException;
		}

		// Check if the price value is correct
		if ( ! is_numeric($price))
		{
			throw new CartInvalidPriceException;
		}

		// Get the cart contents
		$cart = $this->getContent();

		// Get this item options
		$options = ! empty($item['options']) ? $item['options'] : array();

		// Generate the unique row id
		$rowId = $this->generateRowId($item['id'], $options);

		// Make sure that the quantity value is rounded
		$quantity = round($quantity);

		if ($this->itemExists($rowId))
		{
			// Get the item
			$row = $this->getItem($rowId);

			// Update the item quantity
			$row->put('quantity', $row->quantity + $quantity);
		}
		else
		{
			// Create a new item
			$row = new ItemCollection(array(
				'rowId'    => $rowId,
				'id'       => $item['id'],
				'name'     => $item['name'],
				'quantity' => $quantity,
				'price'    => $price,
				'options'  => new ItemOptionsCollection($options),
			));
		}

		// Update the item subtotal
		$row->put('subtotal', (float) $row->quantity * $row->price);

		// Add the item to the cart
		$cart->put($rowId, $row);

		// Update the cart contents
		$this->updateCart($cart);

		return $cart;
	}

	/**
	 * Remove an item or items from the cart.
	 *
	 * @return bool
	 * @throws Cartalyst\Cartify\Exceptions\CartItemNotFoundException
	 */
	public function remove()
	{
		$items = func_get_args();

		if ($this->isMulti($items))
		{
			foreach ($items[0] as $rowId)
			{
				$this->removeItem($rowId);
			}
		}
		else
		{
			foreach ($items as $rowId)
			{
				$this->removeItem($rowId);
			}
		}

		return true;
	}

	/**
	 * Remove an item from the cart.
	 *
	 * @param  string  $rowId
	 * @return bool
	 * @throws Cartalyst\Cartify\Exceptions\CartItemNotFoundException
	 */
	protected function removeItem($rowId)
	{
		// Do we have an array of items to be removed?
		if (is_array($rowId))
		{
			foreach ($rowId as $item)
			{
				$this->remove($item);
			}

			return true;
		}

		// Check if the item exists
		if ( ! $this->itemExists($rowId))
		{
			throw new CartItemNotFoundException;
		}

		// Get the cart contents
		$cart = $this->getContent();

		// Remove the item from the cart
		$cart->forget($rowId);

		// Update the cart contents
		return $this->updateCart($cart);
	}

	/**
	 * Updates an item that is on the cart.
	 *
	 * @param  string  $rowId
	 * @param  array   $attributes
	 * @return bool
	 * @throws Cartalyst\Cartify\Exceptions\CartItemNotFoundException
	 */
	public function update($rowId, $attributes = null)
	{
		// Do we have an array of items to be updated?
		if (is_array($rowId))
		{
			foreach ($rowId as $item => $attributes)
			{
				$this->validateIndexes($attributes);

				$this->update($item, $attributes);
			}

			return true;
		}

		// Check if the item exists
		if ( ! $this->itemExists($rowId))
		{
			throw new CartItemNotFoundException;
		}

		// Get the cart contents
		$cart = $this->getContent();

		// Get the item we want to update
		$row = $cart->get($rowId);

		// Do we have multiple item attributes?
		if (is_array($attributes))
		{
			foreach ($attributes as $key => $value)
			{
				if ($key === 'quantity')
				{
					$value = round($value);
				}

				$row->put($key, $value);
			}
		}

		// We are probably updating the quantity
		else
		{
			$row->put('quantity', (int) round($attributes));
		}

		// Should we update the item subtotal?
		if ( ! is_null(array_keys($attributes, array('quantity', 'price'))))
		{
			$row->put('subtotal', (float) $row->quantity * $row->price);
		}

		// If quantity is less than one, we remove the item
		if ($row->quantity < 1)
		{
			$cart->forget($rowId);
		}
		else
		{
			$cart->put($rowId, $row);
		}

		return $cart;
	}

	/**
	 * Empties the cart.
	 *
	 * @return void
	 */
	public function destroy()
	{
		$this->updateCart(null);
	}

	/**
	 * Returns information about an item.
	 *
	 * @param  string  $rowId
	 * @return Cartalyst\Cartify\Collections\ItemCollection
	 * @throws Cartalyst\Cartify\Exceptions\CartItemNotFoundException
	 */
	public function getItem($rowId)
	{
		// Check if the item exists
		if ( ! $this->itemExists($rowId))
		{
			throw new CartItemNotFoundException;
		}

		// Get the cart contents
		$cart = $this->getContent();

		// Return the item
		return $cart->get($rowId);
	}

	/**
	 * Return the cart total.
	 *
	 * @return float
	 */
	public function getTotal()
	{
		$items = $this->getContent();

		$total = 0;

		foreach ($items as $item)
		{
			$total += $item->subtotal;
		}

		return (float) $total;
	}

	/**
	 * Return the total items on the cart.
	 *
	 * @return int
	 */
	public function getTotalItems()
	{
		$items = $this->getContent();

		return (int) $items->count();
	}

	/**
	 * Return the cart contents.
	 *
	 * @return Cartalyst\Cartify\Collections\CartCollection
	 */
	public function getContent()
	{
		$instance = $this->getInstance();

		return $this->session->has($instance) ? $this->session->get($instance) : new CartCollection;
	}

	/**
	 * Return the current cart instance.
	 *
	 * @return string
	 */
	public function getInstance()
	{
		$sessionKey = $this->getSessionKey();

		return "{$sessionKey}.{$this->instance}";
	}

	/**
	 * Return all the cart instances.
	 *
	 * @return array
	 */
	public function getInstances()
	{
		$sessionKey = $this->getSessionKey();

		return $this->session->get($sessionKey) ?: array();
	}

	/**
	 * Change the cart instance.
	 *
	 * @return Cartalyst\Cartify\Cartify
	 */
	public function instance($instance)
	{
		$this->instance = $instance;

		return $this;
	}

	/**
	 * Remove the cart instance.
	 *
	 * @return bool
	 */
	public function forgetInstance($instance)
	{
		$sessionKey = $this->getSessionKey();

		$this->session->forget("{$sessionKey}.{$instance}");

		return true;
	}

	/**
	 * Check if an item exists in the cart.
	 *
	 * @param  string  $rowId
	 * @return bool
	 */
	protected function itemExists($rowId)
	{
		return $this->getContent()->has($rowId);
	}

	/**
	 * Updates the cart.
	 *
	 * @param  Cartalyst\Cartify\Collections\CartCollection
	 * @return void
	 */
	protected function updateCart($cart)
	{
		$instance = $this->getInstance();

		$this->session->put($instance, $cart);
	}

	/**
	 * Return the list of required indexes.
	 *
	 * @return array
	 */
	public function getRequiredIndexes()
	{
		return $this->requiredIndexes;
	}

	/**
	 * Set required indexes.
	 *
	 * By default it will merge the new indexes with the current
	 * indexes, you can change this behavior by setting false
	 * as the second parameter.
	 *
	 * @param  bool  $merge
	 * @return void
	 */
	public function setRequiredIndexes($indexes, $merge = true)
	{
		$currentIndexes = $merge ? $this->requiredIndexes : array();

		$this->requiredIndexes = array_unique(array_merge($currentIndexes, $indexes));
	}

	/**
	 * Return the session key.
	 *
	 * @return string
	 */
	public function getSessionKey()
	{
		return $this->sessionKey;
	}

	/**
	 * Set the session key.
	 *
	 * @return void
	 */
	public function setSessionKey($key)
	{
		$this->sessionKey = $key;
	}

	/**
	 * Generate a unique identifier base on the item data.
	 *
	 * @param  string  $id
	 * @param  array   $options
	 * @return string
	 */
	protected function generateRowId($id, $options)
	{
		return md5($id.serialize($options));
	}

	/**
	 * Checks if the provided array is a multidimensional array.
	 *
	 * @param  array  $array
	 * @return bool
	 */
	protected function isMulti($array)
	{
		$array = array_shift($array);

		return is_array($array);
	}

	/**
	 * Validates if the provided arguments contains all the required indexes.
	 *
	 * @param  array  $arguments
	 * @return void
	 * @throws Cartalyst\Cartify\Exceptions\CartInvalidDataException
	 */
	protected function validateIndexes($arguments)
	{
		foreach ($this->getRequiredIndexes() as $parameter)
		{
			if ( ! array_key_exists($parameter, $arguments))
			{
				throw new CartInvalidDataException;
			}
		}
	}

}
