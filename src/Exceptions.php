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
 * @version    2.0.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Cart\Exceptions;

use Exception;

class CartInvalidAttributesException extends Exception
{
}
class CartInvalidPriceException extends Exception
{
}
class CartInvalidQuantityException extends Exception
{
}
class CartItemNotFoundException extends Exception
{
}
class CartMissingRequiredIndexException extends Exception
{
}
