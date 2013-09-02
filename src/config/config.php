<?php
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

return array(

	/*
	|--------------------------------------------------------------------------
	| Default Storage Driver
	|--------------------------------------------------------------------------
	|
	| This option controls the default storage "driver" that will be used on
	| requests. By default, we will use the lightweight session driver but
	| you may specify any of the other wonderful drivers provided here.
	|
	| Supported: "session", "cookie", "database"
	|
	*/

	'driver' => 'session',

	/*
	|--------------------------------------------------------------------------
	| Session
	|--------------------------------------------------------------------------
	|
	| Configuration specific to the session component of Cart.
	|
	*/

	'session' => array(

		/*
		|--------------------------------------------------------------------------
		| Default Session Key
		|--------------------------------------------------------------------------
		|
		| This option allows you to specify the default session key used by Cart.
		|
		*/

		'key' => 'cartalyst_cart',

 	),

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
