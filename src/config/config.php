<?php
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

return array(

	/*
	|--------------------------------------------------------------------------
	| Session Key
	|--------------------------------------------------------------------------
	|
	| Session key that is used to store the cart information into the current
	| session. This can be changed if it conflicts with another key.
	|
	*/

	'session' => 'cartify',

	/*
	|--------------------------------------------------------------------------
	| Default Cart Instance
	|--------------------------------------------------------------------------
	|
	| Define here the name of the default cart instance.
	|
	*/

	'instance' => 'main',

	/*
	|--------------------------------------------------------------------------
	| Required Indexes
	|--------------------------------------------------------------------------
	|
	| Here you can define all the indexes that are required to be passed
	| when adding or updating items.
	|
	*/

	'requiredIndexes' => array(),

);
