<?php namespace Cartalyst\Cartify\Cart;

use Cartalyst\Cartify\Sessions\SessionInterface;

class Cart {

	/**
	 * The session driver used by Cartify.
	 *
	 * @var Cartalyst\Cartify\Sessions\SessionInterface
	 */
	protected $session;

	/**
	 * Holds the cart contents.
	 *
	 * @var array
	 */
	protected $contents = array();

	/**
	 * Regular expression to validate item ID's.
	 *
	 *  Allowed:
	 *      alpha-numeric
	 *      dashes
	 *      underscores
	 *      periods
	 *
	 * @access   protected
	 * @var      string
	 */
	protected $item_id_rules = '\.a-z0-9_-';

	/**
	 * Create a new Cartify instance.
	 *
	 * @param  Cartalyst\Cartify\Sessions\SessionInterface  $session
	 * @return void
	 */
	public function __construct(SessionInterface $session)
	{
		//
		$this->session = $session;

		// Grab the shopping cart array from the session.
		$this->contents = $this->session->get();

		// We don't have any cart session, set some base values.
		if (is_null($this->contents))
		{
			$this->contents = array('cart_total' => 0, 'total_items' => 0);
		}
	}

	/**
	 * Add a new item into the cart. If the item already
	 * exists, this item quantity will be updated.
	 *
	 * @param  array  $item
	 * @return string
	 * @throws Cartalyst\Cartify\Cart\InvalidDataException
	 * @throws Cartalyst\Cartify\Cart\RequiredIndexException
	 * @throws Cartalyst\Cartify\Cart\InvalidItemQuantityException
	 * @throws Cartalyst\Cartify\Cart\InvalidItemIdException
	 * @throws Cartalyst\Cartify\Cart\InvalidItemPriceException
	 */
	public function insert($itemData = array())
	{
		// Check if we have data
		if ( ! is_array($itemData) or count($itemData) == 0)
		{
			throw new InvalidDataException;
		}

		// Required indexes
		$required_indexes = array('id', 'quantity', 'price', 'name');

		// Loop through the required indexes
		foreach ($required_indexes as $index)
		{
			// Make sure the array contains this index
			if (empty($itemData[$index]))
			{
				throw new RequiredIndexException("Required index [$index] is missing.");
			}
		}

		// Remove leading zeros and anything that isn't a
		// number or decimal point from the quantity.
		$itemData['quantity'] = (float) $itemData['quantity'];

		// If the quantity is zero or blank there's nothing for us to do
		if ( ! is_numeric($itemData['quantity']) or $itemData['quantity'] == 0)
		{
			throw new InvalidItemQuantityException;
		}

		// Validate the item id
		if ( ! preg_match("/^[$this->item_id_rules]+$/i", $itemData['id']))
		{
			throw new InvalidItemIdException;
		}

		// Remove leading zeros and anything that isn't a
		// number or decimal point from the price.
		$itemData['price'] = (float) $itemData['price'];

		// Is the price a valid number?
		if ( ! is_numeric($itemData['price']))
		{
			throw new InvalidItemPriceException;
		}

		// Generate Item Unique Identifier
		$itemRowId = $this->generateUid($itemData);

		// If the item already exists, we update this item quantity.
		if ($this->checkItemExists($itemRowId))
		{
			$newquantity = (int) $itemData['quantity'] + $this->contents[$itemRowId]['quantity'];

			return $this->update($itemRowId, array('quantity' => $newquantity));
		}

		// Store the item in the shopping cart
		$this->contents[$itemRowId] = $itemData;

		// Update the cart
		$this->updateCart();

		// Item added with success
		return $itemRowId;
	}

	/**
	 * Updates an item in the cart.
	 *
	 * @param  string  $itemRowId
	 * @param  array   $itemData
	 * @return bool
	 * @throws Cartalyst\Cartify\Cart\InvalidItemRowIdException
	 * @throws Cartalyst\Cartify\Cart\ItemNotFoundException
	 * @throws Cartalyst\Cartify\Cart\InvalidDataException
	 * @throws Cartalyst\Cartify\Cart\InvalidItemQuantityException
	 */
	public function update($itemRowId = null, $itemData = array())
	{
		// Check if we have a valid id
		if (is_null($itemRowId))
		{
			throw new InvalidItemRowIdException;
		}

		// Check if the item exists
		if ( ! $this->checkItemExists($itemRowId))
		{
			throw new ItemNotFoundException;
		}

		// Check if we have data
		if ( ! is_array($itemData) or count($itemData) === 0)
		{
			throw new InvalidDataException;
		}

		// Prepare the quantity
		$quantity = (float) $itemData['quantity'];

		// Unset the quantity
		unset($itemData['quantity']);

		// Is the quantity a number ?
		if ( ! is_numeric($quantity))
		{
			throw new InvalidItemQuantityException;
		}

		// Check if we have more data, like options or custom data
		if ( ! empty($itemData))
		{
			// Loop through the item data.
			foreach ($itemData as $key => $val)
			{
				// Update the item data.
				$this->contents[$itemRowId][$key] = $val;
			}
		}

		// If the new quantity is the same as the already
		// in the cart, there is nothing else to update.
		if ($this->contents[$itemRowId]['quantity'] == $quantity)
		{
			return true;
		}

		// If the quantity is zero or less, we will be removing
		// the item from the cart.
		if ($quantity <= 0)
		{
			// Remove the item from the cart.
			unset($this->contents[$itemRowId]);
		}

		// Quantity is greater than zero, let's update the item cart.
		else
		{
			// Update the item quantity.
			$this->contents[$itemRowId]['quantity'] = $quantity;
		}

		// Update the cart
		$this->updateCart();

		// Cart updated with success.
		return true;
	}

	/**
	 * Remove an item from the cart.
	 *
	 * @param  string  $itemRowId
	 * @return bool
	 * @throws Cartalyst\Cartify\Cart\InvalidItemRowIdException
	 * @throws Cartalyst\Cartify\Cart\ItemNotFoundException
	 */
	public function remove($itemRowId = null)
	{
		// Check if we have a valid id
		if (is_null($itemRowId))
		{
			throw new InvalidItemRowIdException;
		}

		// Check if the item exists
		if ( ! $this->checkItemExists($itemRowId))
		{
			throw new ItemNotFoundException;
		}

		// Remove the item
		$this->update($itemRowId, array('quantity' => 0));

		// Item sucessfully removed
		return true;
	}

	/**
	 * Empties the cart, and removes the session.
	 *
	 * @return void
	 */
	public function destroy()
	{
		// Remove all the data from the cart and set some base values
		$this->contents = array('cart_total' => 0, 'total_items' => 0);

		// Remove the session.
		$this->session->forget();
	}

	/**
	 * Returns the cart total.
	 *
	 * @return int
	 */
	public function getTotal()
	{
		return $this->contents['cart_total'];
	}

	/**
	 * Returns the total item count.
	 *
	 * @return int
	 */
	public function getTotalItems()
	{
		return $this->contents['total_items'];
	}

	/**
	 * Returns the cart contents.
	 *
	 * @return array
	 */
	public function getContents()
	{
		// Get the cart contents
		$cart = $this->contents;

		// Remove these so they don't create a problem
		// when showing the cart table.
		unset($cart['total_items']);
		unset($cart['cart_total']);

		// Return the cart contents
		return $cart;
	}

	/**
	 * Check if an item exists in the cart.
	 *
	 * @param  string  $itemRowId
	 * @return bool
	 */
	public function checkItemExists($itemRowId = null)
	{
		return ! empty($this->contents[$itemRowId]);
	}

	/**
	 * Returns information about an item.
	 *
	 * @param  string  $itemRowId
	 * @return array
	 * @throws Cartalyst\Cartify\Cart\InvalidItemRowIdException
	 * @throws Cartalyst\Cartify\Cart\ItemNotFoundException
	 */
	public function getItem($itemRowId = null)
	{
		// Check if we have a valid id
		if (is_null($itemRowId))
		{
			throw new InvalidItemRowIdException;
		}

		// Check if the item exists
		if ($this->checkItemExists($itemRowId))
		{
			return $this->contents[$itemRowId];
		}

		throw new ItemNotFoundException;
	}

	/**
	 * Checks if an item has options.
	 *
	 * @param  string  $itemRowId
	 * @throws Cartalyst\Cartify\Cart\InvalidItemRowIdException
	 * @throws Cartalyst\Cartify\Cart\ItemNotFoundException
	 */
	public function checkItemHasOptions($itemRowId = null)
	{
		// Check if we have a valid id
		if (is_null($itemRowId))
		{
			throw new InvalidItemRowIdException;
		}

		// Check if the item exists
		if ($this->checkItemExists($itemRowId))
		{
			return ! empty($this->contents[$itemRowId]['options']);
		}

		throw new ItemNotFoundException;
	}

	/**
	 * Returns an array of options, for a particular item row id.
	 *
	 * @param  string  $itemRowId
	 * @throws Cartalyst\Cartify\Cart\InvalidItemRowIdException
	 * @throws Cartalyst\Cartify\Cart\ItemNotFoundException
	 */
	public function getItemOptions($itemRowId = null)
	{
		// Check if we have a valid id
		if (is_null($itemRowId))
		{
			throw new InvalidItemRowIdException;
		}

		// Check if the item exists
		if ($this->checkItemExists($itemRowId))
		{
			return $this->contents[$itemRowId]['options'];
		}

		throw new ItemNotFoundException;
	}

	/**
	 * Generate a unique identifier base on the item data.
	 *
	 * @param  array  $itemData
	 * @return string
	 */
	public function generateUid($itemData)
	{
		$itemRowId = $itemData['id'];

		if ( ! empty($itemData['options']))
		{
			$itemRowId .= implode('', $itemData['options']);
		}

		return md5($itemRowId);
	}

	/**
	 * Updates the cart session.
	 *
	 * @return bool
	 */
	protected function updateCart()
	{
		// Reset cart total and cart total items
		$this->contents['cart_total']  = 0;
		$this->contents['total_items'] = 0;

		// Loop through the cart items
		foreach ($this->contents as $rowId => $item)
		{
			// Get some item data
			$price    = $item['price'];
			$quantity = $item['quantity'];

			// If this item quantity is less than or equals to 0,
			// we should not count this item.
			if ($quantity <= 0)
			{
				continue;
			}

			// Calculations
			$this->contents['cart_total']  += ($price * $quantity);
			$this->contents['total_items'] += $quantity;

			// Set the subtotal of this item
			$this->contents[$rowId]['subtotal'] = ($price * $quantity);
		}

		// Is our cart empty?
		if (count($this->contents) <= 2)
		{
			// If so we delete it from the session
			$this->destroy();
		}
		else
		{
			// Update the cart session data
			$this->session->put($this->contents);
		}

		// Cart sucessfully updated
		return true;
	}

}
