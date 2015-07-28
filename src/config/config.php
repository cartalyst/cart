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
 * @version    1.1.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Session Key
    |--------------------------------------------------------------------------
    |
    | This option allows you to specify the default session key used by the Cart.
    |
    */

    'session_key' => 'cartalyst_cart',

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

    'requiredIndexes' => [],

];
