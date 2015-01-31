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

namespace Cartalyst\Cart\tests;

use Cartalyst\Cart\Cart;
use Illuminate\Session\Store;
use PHPUnit_Framework_TestCase;
use Illuminate\Events\Dispatcher;
use Cartalyst\Conditions\Condition;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\FileSessionHandler;
use Cartalyst\Cart\Storage\IlluminateSession;

abstract class CartTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the cart instance.
     *
     * @var \Cartalyst\Cart\Cart
     */
    protected $cart;

    /**
     * Setup resources and dependencies
     */
    public function setUp()
    {
        $sessionHandler = new FileSessionHandler(new Filesystem, __DIR__.'/storage/sessions');

        $session = new IlluminateSession(new Store('cartalyst_cart_session', $sessionHandler));

        $this->cart = new Cart('cart', $session, new Dispatcher);
    }

    /**
     * Creates an item.
     *
     * @param  string  $name
     * @param  float  $price
     * @param  int  $quantity
     * @param  array  $conditions
     * @param  array  $attrPrices
     * @param  int  $weight
     * @return array
     */
    protected function createItem(
        $name = 'Foobar',
        $price = 0,
        $quantity = 1,
        $conditions = [],
        $attrPrices = [0, 0],
        $weight = 0
    ) {
        return [
            'id'         => strtolower(str_replace(' ', '', $name)),
            'name'       => $name,
            'quantity'   => $quantity,
            'conditions' => $conditions,
            'price'      => $price,
            'weight'     => $weight,
            'attributes' => [
                'size' => [
                    'label' => 'Large',
                    'value' => 'l',
                    'price' => $attrPrices[0],
                ],
                'color' => [
                    'label' => 'Red',
                    'value' => 'red',
                    'price' => $attrPrices[1],
                ],
            ],
        ];
    }

    /**
     * Creates a condition.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  int  $value
     * @param  string  $target
     * @param  array  $rules
     * @param  bool  $inclusive
     * @return \Cartalyst\Conditions\Condition
     */
    protected function createCondition(
        $name,
        $type,
        $value,
        $target = 'subtotal',
        $rules = null,
        $inclusive = false
    ) {
        $condition = new Condition(compact('name', 'type', 'target'));

        if (is_array($value)) {
            $actions = [];

            foreach ($value as $val) {
                $actions[]['value'] = $val;
            }

            $actions[]['inclusive'] = $inclusive;
        } else {
            $actions = compact('value', 'inclusive');
        }

        $condition->setActions($actions);

        if ($rules) {
            $condition->setRules([
                $rules,
            ]);
        }

        return $condition;
    }
}
