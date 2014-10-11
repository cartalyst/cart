<?php namespace Cartalyst\Cart\Exceptions;
/**
 * Part of the Cart package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Cart
 * @version    1.0.6
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Exception;

class CartInvalidAttributesException extends Exception {}
class CartInvalidPriceException extends Exception {}
class CartInvalidQuantityException extends Exception {}
class CartItemNotFoundException extends Exception {}
class CartMissingRequiredIndexException extends Exception {}
