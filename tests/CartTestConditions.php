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

namespace Cartalyst\Cart\tests;

class CartTestConditions extends CartTestCase
{
    /** @test */
    public function cart_handles_all_defined_condition_types()
    {
        $discount = $this->createCondition('Discount 5%', 'discount', '-5.00%');
        $other    = $this->createCondition('Other 5', 'other', 5, 'price');
        $tax      = $this->createCondition('Tax 10%', 'tax', '10%');

        $item = $this->cart->add(
            $this->createItem('Foobar 1', 100, 5, [$discount, $other, $tax])
        );

        $this->assertEquals($item->total('discount'), 498.75);
        $this->assertEquals($item->subtotal(), 500);
        $this->assertEquals($item->conditionsTotalSum('discount'), -26.25);
        $this->assertEquals($item->conditionsTotalSum('tax'), 49.875);
        $this->assertEquals($item->total(), 548.625);

        $this->assertCount(3, $item->conditions());
        $this->assertCount(1, $item->conditions('discount'));
        $this->assertCount(3, $this->cart->itemsConditions());

        $this->assertEquals($this->cart->itemsConditionsTotalSum('discount', false), -26.25);
        $this->assertEquals($this->cart->itemsConditionsTotalSum('tax'), 49.875);
        $this->assertEquals($this->cart->itemsSubtotal(), 500);
        $this->assertEquals($this->cart->subtotal(), 548.625);
        $this->assertEquals($this->cart->total(), 548.625);

        $this->cart->condition($tax);

        $this->assertCount(4, $this->cart->conditions());
        $this->assertCount(1, $this->cart->conditions(null, false));

        $this->assertEquals($this->cart->itemsConditionsTotalSum(), 48.625);
        $this->assertEquals($this->cart->itemsConditionsTotalSum('discount'), -26.25);
        $this->assertEquals($this->cart->itemsConditionsTotalSum('other'), 25);
        $this->assertEquals($this->cart->itemsConditionsTotalSum('tax'), 49.875);

        $this->assertEquals($this->cart->conditionsTotalSum(), 103.4875);
        $this->assertEquals($this->cart->conditionsTotalSum('discount'), -26.25);
        $this->assertEquals($this->cart->conditionsTotalSum('other'), 25);
        $this->assertEquals($this->cart->conditionsTotalSum('tax'), 104.7375);

        $this->cart->condition($discount);

        $this->assertEquals($this->cart->conditionsTotalSum(), 73.313125);
        $this->assertEquals($this->cart->conditionsTotalSum('discount'), -53.68125);
        $this->assertEquals($this->cart->conditionsTotalSum('other'), 25);
        $this->assertEquals($this->cart->conditionsTotalSum('tax'), 101.994375);

        $item->removeConditions('tax');

        $this->assertEquals($item->total(), 498.75);

        $item->removeConditions();

        $this->assertEquals($item->total(), 500);
    }

    /** @test */
    public function cart_calculates_condition_totals()
    {
        $discount = $this->createCondition('Discount 5%', 'discount', '-5%');
        $other1   = $this->createCondition('Add5', 'other', 5, 'price');
        $other2   = $this->createCondition('Add5%', 'other', '5%', 'price');
        $other3   = $this->createCondition('Other 10%', 'other', '10%');
        $tax      = $this->createCondition('Tax 10%', 'tax', '10%');

        $this->cart->add(
            $this->createItem('Foobar', 100, 5, [$other1, $other2])
        );

        $this->cart->condition([$discount, $other3, $tax]);

        $this->assertEquals($this->cart->total(), 632.225);
    }

    /** @test */
    public function cart_applies_tax_on_price()
    {
        $tax      = $this->createCondition('Tax 5%', 'tax', '5%', 'price');
        $shipping = $this->createCondition('Shipping', 'shipping', '10%');

        $item = $this->cart->add(
            $this->createItem('Foobar', 100, 5, $tax)
        );

        $this->assertEquals($item->conditionsTotalSum('tax'), 25);
        $this->assertEquals($item->total(), 525);

        $item->condition($shipping);

        $item->setConditionsOrder([
            'tax',
            'shipping'
        ]);

        $this->assertEquals($item->total(), 577.5);

        $item->setConditionsOrder([
            'tax',
            'other',
            'discount'
        ]);

        $this->assertEquals($item->total(), 525);
    }

    /** @test */
    public function cart_applies_conditions_on_items()
    {
        $discount = $this->createCondition('Discount 10%', 'discount', '-10', 'subtotal', 'price <= 125');

        $items = $this->cart->add([
            $this->createItem('Foobar 1', 97, 1, $discount, [0, 3]),
            $this->createItem('Foobar 2', 85, 1, $discount, [15, 0]),
        ]);

        $this->assertEquals($items[0]->total(), 90);
        $this->assertEquals($items[1]->total(), 90);
        $this->assertEquals($this->cart->itemsSubtotal(), 200);
        $this->assertEquals($this->cart->total(), 180.00);
    }

    /** @test */
    public function cart_applies_conditions_on_cart()
    {
        $discount = $this->createCondition('Discount 10%', 'discount', '-10%');
        $tax1     = $this->createCondition('Tax 10%', 'tax', '10%');
        $tax2     = $this->createCondition('Tax 12%', 'tax', '12%');

        $this->cart->add(
            $this->createItem('Foobar', 125, 1, $tax1, [0, 3])
        );

        $this->cart->condition([$discount, $tax2]);

        $this->assertEquals($this->cart->total(), 141.9264);
        $this->assertEquals($this->cart->subtotal(), 140.8);
    }

    /** @test */
    public function cart_handles_conditions_with_no_action()
    {
        $condition = $this->createCondition('Free product', 'other', null);

        $condition->forget('actions');

        $this->cart->add(
            $this->createItem('Foobar', 125, 2)
        );

        $this->cart->condition($condition);

        $this->assertEquals($this->cart->total(), 250);
        $this->assertEquals($this->cart->subtotal(), 250);
    }

    /** @test */
    public function cart_calculates_item_total()
    {
        $tax = $this->createCondition('Tax 10%', 'tax', '10%');

        $item = $this->cart->add(
            $this->createItem('Foobar', 125, 2, $tax, [3, 3])
        );

        $this->assertEquals($item->subtotal(), 262);
        $this->assertEquals($item->total(), 288.2);
    }

    /** @test */
    public function cart_applies_multiple_conditions_on_items()
    {
        $discount = $this->createCondition('Discount 5% + 2', 'discount', ['-5%', '-2']);
        $tax      = $this->createCondition('Tax 10%', 'tax', '10%');

        $item = $this->cart->add(
            $this->createItem('Foobar', 125, 2, [$tax, $discount], [0, 3])
        );

        $this->assertEquals($item->subtotal(), 256);
        $this->assertEquals($item->total(), 265.32);
    }

    /** @test */
    public function cart_applies_tax_conditions()
    {
        $discount = $this->createCondition('Discount 5%', 'discount', '-5%');
        $other    = $this->createCondition('Other 5', 'other', '5', 'price');
        $tax1     = $this->createCondition('Tax 10%', 'tax', '10%');
        $tax2     = $this->createCondition('Tax 5%', 'tax', '5%');

        $items = $this->cart->add([
            $this->createItem('Foobar 1', 97, 2, $tax1, [0, 3]),
            $this->createItem('Foobar 2', 85, 2, [$discount, $other, $tax1, $tax2]),
        ]);

        $this->assertEquals($items[0]->subtotal(), 200);
        $this->assertEquals($items[0]->conditionsTotalSum('tax'), 20);
        $this->assertEquals($items[0]->conditionsTotalSum('discount'), 0);
        $this->assertEquals($items[0]->total(), 220);

        $this->assertEquals($items[1]->subtotal(), 170);
        $this->assertEquals($items[1]->conditionsTotalSum('tax'), 25.65);
        $this->assertEquals($items[1]->conditionsTotalSum('discount'), -9);
        $this->assertEquals($items[1]->total(), 196.65);

        $this->cart->condition($tax2);

        $this->assertEquals($this->cart->subtotal(), 416.65);
        $this->assertEquals($this->cart->itemsSubtotal(), 370);
        $this->assertEquals($this->cart->total(), 437.4825);
        $this->assertEquals($this->cart->conditionsTotalSum('tax', false), 20.8325);
        $this->assertEquals($this->cart->conditionsTotalSum('tax'), 66.4825);
        $this->assertEquals($this->cart->conditionsTotalSum('discount'), -9);
        $this->assertEquals($this->cart->itemsConditionsTotalSum('tax'), 45.65);
    }

    /** @test */
    public function cart_applies_multiple_types_of_conditions_on_items()
    {
        $discount = $this->createCondition('Discount 10%', 'discount', '-10%', 'price');
        $other1   = $this->createCondition('Other 5', 'other', '5%', 'price');
        $other2   = $this->createCondition('Other 5', 'other', '5%');
        $tax      = $this->createCondition('Tax 10%', 'tax', '10%');

        $items = $this->cart->add([
            $this->createItem('Foobar 1', 100, 4, $discount, [0, 3]),
            $this->createItem('Foobar 2', 100, 2, [$discount, $other1], [15, 0]),
        ]);

        $this->assertEquals($items[0]->subtotal(), 412);
        $this->assertEquals($items[0]->conditionsTotalSum('discount'), -40);
        $this->assertEquals($items[0]->total(), 372);

        $this->assertEquals($items[1]->subtotal(), 230);
        $this->assertEquals($items[1]->conditionsTotalSum('discount'), -20);
        $this->assertEquals($items[1]->total(), 219);

        $this->assertEquals($this->cart->subtotal(), 591);
        $this->assertEquals($this->cart->itemsSubtotal(), 642);
        $this->assertEquals($this->cart->total(), 591);

        $this->cart->condition($tax);

        $this->assertEquals($this->cart->subtotal(), 591);
        $this->assertEquals($this->cart->total(), 650.1);

        $this->cart->condition($other2);

        $this->assertEquals($this->cart->subtotal(), 591);
        $this->assertEquals($this->cart->total(), 682.605);
    }

    /** @test */
    public function cart_applies_conditions_on_item_price()
    {
        $other = $this->createCondition('Other 5', 'other', '5', 'price');
        $tax   = $this->createCondition('Tax 10%', 'tax', '10%');

        $item = $this->cart->add(
            $this->createItem('Foobar', 125, 3, $other, [3, 3])
        );

        $this->assertEquals($item->subtotal(), 393);
        $this->assertEquals($item->total(), 408);
        $this->assertEquals($this->cart->subtotal(), 408);
        $this->assertEquals($this->cart->total(), 408);

        $this->cart->condition($tax);

        $this->assertEquals($this->cart->subtotal(), 408);
        $this->assertEquals($this->cart->total(), 448.8);
    }

    /** @test */
    public function cart_ignores_item_conditions_if_not_valid()
    {
        $other = $this->createCondition('Other 5', 'other', '5', 'price', 'price > 200');

        $item = $this->cart->add(
            $this->createItem('Foobar', 125, 3, $other, [3, 3])
        );

        $this->assertEquals($item->subtotal(), 393);
        $this->assertEquals($item->total(), 393);
    }

    /** @test */
    public function cart_handles_condition_rules_if_valid()
    {
        $tax   = $this->createCondition('Tax 10%', 'tax', '10%');
        $other = $this->createCondition('Other 5', 'other', '5', 'price', 'price > 200');

        $items = $this->cart->add([
            $this->createItem('Foobar 1', 244, 3, [$tax, $other], [3, 3]),
            $this->createItem('Foobar 2', 125, 3, $other, [3, 3])
        ]);

        $this->assertEquals($items[0]->subtotal(), 750);
        $this->assertEquals($items[0]->total(), 841.5);

        $this->assertEquals($items[1]->subtotal(), 393);
        $this->assertEquals($items[1]->total(), 393);

        $this->assertEquals($this->cart->subtotal(), 1234.5);
        $this->assertEquals($this->cart->total(), 1234.5);
    }

    /** @test */
    public function cart_handles_condition_rules_on_subtotal()
    {
        $condition = $this->createCondition('Subtotal', 'other', '-5', 'subtotal', 'subtotal > 50');

        $item = $this->createItem('Foobar 1', 20, 3, [$condition]);

        $item = $this->cart->add($item);

        $this->assertEquals($item->subtotal(), 60);
        $this->assertEquals($item->total(), 55);

        $this->assertEquals($this->cart->subtotal(), 55);
        $this->assertEquals($this->cart->total(), 55);
    }

    /** @test */
    public function cart_applies_multiple_conditions_on_items_and_cart()
    {
        $discount1 = $this->createCondition('Discount 5%', 'discount', '-5%');
        $discount2 = $this->createCondition('Discount 10%', 'discount', '-10%');
        $other     = $this->createCondition('Other 5', 'other', '5', 'price', 'price > 200');

        $items = $this->cart->add([
            $this->createItem('Foobar 1', 244, 3, [$discount1, $other], [3, 3]),
            $this->createItem('Foobar 2', 125, 3, $other, [3, 3]),
        ]);

        $this->assertEquals($items[0]->subtotal(), 750);
        $this->assertEquals($items[0]->conditionsTotalSum('discount'), -38.25);
        $this->assertEquals($items[0]->total(), 726.75);

        $this->assertEquals($items[1]->subtotal(), 393);
        $this->assertEquals($items[1]->conditionsTotalSum('other'), 0);
        $this->assertEquals($items[1]->total(), 393);

        $this->assertEquals($this->cart->subtotal(), 1119.75);
        $this->assertEquals($this->cart->itemsSubtotal(), 1143);
        $this->assertEquals($this->cart->total(), 1119.75);

        $this->cart->condition($discount2);

        $this->assertEquals($this->cart->subtotal(), 1119.75);
        $this->assertEquals($this->cart->total(), 1007.775);
        $this->assertEquals($this->cart->conditionsTotalSum('discount', false), -111.975);
        $this->assertEquals($this->cart->conditionsTotalSum('discount'), -150.225);
    }

    /** @test */
    public function cart_removes_conditions_from_items()
    {
        $tax   = $this->createCondition('Tax 10%', 'tax', '10%');
        $other = $this->createCondition('Other 5', 'other', '5', 'price');

        $items = $this->cart->add([
            $this->createItem('Foobar 1', 244, 3, [$tax, $other], [3, 3]),
            $this->createItem('Foobar 2', 125, 3, $other, [3, 3]),
        ]);

        $this->assertEquals($items[0]->total(), 841.5);
        $this->assertEquals($items[1]->total(), 408);

        $this->cart->update([
            $items[0]['rowId'] => [
                'conditions' => $tax
            ]
        ]);

        $this->assertEquals($items[0]->total(), 825);

        $this->cart->update([
            $items[1]['rowId'] => [
                'conditions' => null
            ]
        ]);

        $this->assertEquals($items[1]->total(), 393);

        $this->cart->update([
            $items[1]['rowId'] => [
                'conditions' => [$tax, $other]
            ]
        ]);

        $this->assertEquals($items[1]->total(), 448.8);

        $this->cart->update([
            $items[1]['rowId'] => [
                'weights' => 20.00
            ]
        ]);

        $this->assertEquals($items[1]->total(), 448.8);
    }

    /** @test */
    public function cart_removes_conditions_from_cart()
    {
        $tax   = $this->createCondition('Tax 10%', 'tax', '10%');
        $other = $this->createCondition('Other 10%', 'other', '10%');

        $this->cart->add([
            $this->createItem('Foobar 1', 244, 3, $tax, [3, 3]),
            $this->createItem('Foobar 2', 125, 3, null, [3, 3]),
        ]);

        $this->cart->condition($tax);

        $this->assertEquals($this->cart->total(), 1339.8);

        $this->cart->condition([$tax, $other]);

        $this->assertEquals($this->cart->total(), 1473.78);

        $this->cart->removeConditionByType('tax', false);

        $this->assertEquals($this->cart->total(), 1339.8);

        $this->cart->removeConditions(null, false);

        $this->assertEquals($this->cart->total(), 1218);
    }

    /** @test */
    public function cart_adds_removes_conditions_from_items_and_cart()
    {
        $tax1 = $this->createCondition('Tax 10%', 'tax', '10%');
        $tax2 = $this->createCondition('Item Tax 5%', 'tax', '5%', 'price');
        $tax3 = $this->createCondition('Item Tax 10%', 'tax', '10%', 'price');

        $item = $this->cart->add(
            $this->createItem('Foobar 1', 100, 3, [$tax2, $tax3])
        );

        $this->assertEquals($item->total(), 345);

        $this->assertEquals($this->cart->total(), 345);
        $this->assertEquals($this->cart->subtotal(), 345);

        $this->cart->condition($tax1);

        $this->assertEquals($this->cart->subtotal(), 345);
        $this->assertEquals($this->cart->total(), 379.5);
        $this->assertEquals($this->cart->conditionsTotalSum('tax'), 79.5);

        $this->cart->removeConditionByName('Tax 10%');

        $this->assertEquals($this->cart->subtotal(), 345);
        $this->assertEquals($this->cart->total(), 345);
        $this->assertEquals($this->cart->conditionsTotalSum('tax'), 45);

        $this->cart->removeConditionByType('tax');

        $this->assertEquals($this->cart->subtotal(), 300);
        $this->assertEquals($this->cart->total(), 300);
        $this->assertEquals($this->cart->conditionsTotalSum('tax'), 0);

        $this->cart->removeConditions();

        $this->assertEquals($this->cart->subtotal(), 300);
        $this->assertEquals($this->cart->total(), 300);
        $this->assertEquals($this->cart->conditionsTotalSum('tax'), 0);

        $this->cart->update('1085cc7857b8241294e0a45799a1c36e', [
            'quantity' => 3,
        ]);

        $this->assertEquals($this->cart->subtotal(), 300);
        $this->assertEquals($this->cart->total(), 300);
        $this->assertEquals($this->cart->conditionsTotalSum('tax'), 0);
    }

    /** @test */
    public function cart_calculates_discounts()
    {
        $discount = $this->createCondition('Discount 10%', 'discount', '-10%');

        $this->cart->add([
            $this->createItem('Foobar 1', 244, 3, $discount, [3, 3]),
            $this->createItem('Foobar 2', 125, 3, $discount, [3, 3]),
        ]);

        $this->cart->condition($discount);

        $discounts = $this->cart->conditions('discount', false);

        $discountCondition = $discounts[0];

        $this->assertEquals($discountCondition->get('name'), 'Discount 10%');
        $this->assertEquals($discountCondition->get('type'), 'discount');
        $this->assertEquals($discountCondition->get('target'), 'subtotal');
    }

    /** @test */
    public function cart_calculates_taxes()
    {
        $tax = $this->createCondition('Tax 10%', 'tax', '10%');

        $this->cart->add([
            $this->createItem('Foobar 1', 244, 3, $tax, [3, 3]),
            $this->createItem('Foobar 2', 125, 3, $tax, [3, 3]),
        ]);

        $this->cart->condition($tax);

        $taxes = $this->cart->conditions('tax', false);

        $taxCondition = $taxes[0];

        $this->assertEquals($taxCondition->get('name'), 'Tax 10%');
        $this->assertEquals($taxCondition->get('type'), 'tax');
        $this->assertEquals($taxCondition->get('target'), 'subtotal');
    }

    /** @test */
    public function cart_removes_conditions_with_different_targets_from_cart()
    {
        $tax   = $this->createCondition('Tax 10%', 'tax', '10%');
        $other = $this->createCondition('Other 10%', 'other', '10%');

        $tax->put('code', 'foo');

        $this->cart->add([
            $this->createItem('Foobar 1', 244, 3, $tax, [3, 3]),
            $this->createItem('Foobar 2', 125, 3, null, [3, 3]),
        ]);

        $this->cart->condition($tax);

        $this->assertEquals($this->cart->total(), 1339.8);

        $this->cart->condition([$tax, $other]);

        $this->assertEquals($this->cart->total(), 1473.78);

        $this->cart->removeConditions('foo', false, 'code');

        $this->assertEquals($this->cart->total(), 1339.8);

        $this->cart->removeConditions(null, false);

        $this->assertEquals($this->cart->total(), 1218);
    }

    /** @test */
    public function cart_calculates_conditions_total()
    {
        $tax1 = $this->createCondition('Tax 5%', 'tax', '5%');
        $tax2 = $this->createCondition('Tax 10%', 'tax', '10%');

        $this->cart->add([
            $this->createItem('Foobar 1', 244, 3, $tax2, [3, 3]),
            $this->createItem('Foobar 2', 125, 3, $tax2, [3, 3]),
        ]);

        $this->cart->condition([$tax1, $tax2]);

        $conditionsTotal = [
            'Tax 10%' => 125.73,
            'Tax 5%'    => 62.865,
        ];

        $conditionResults = [
            'Tax 10%' => 114.3,
        ];

        $this->assertEquals($this->cart->itemsConditionsTotal('tax'), $conditionResults);
        $this->assertEquals($this->cart->conditionsTotal('tax', false), $conditionsTotal);
    }

    /** @test */
    public function cart_calculates_conditions_separate()
    {
        $tax1 = $this->createCondition('Tax 5%', 'tax', '5%');
        $tax2 = $this->createCondition('Tax 10%', 'tax', '10%');

        $items = $this->cart->add([
            $this->createItem('Foobar 1', 244, 3, $tax2, [3, 3]),
            $this->createItem('Foobar 2', 125, 3, $tax2, [3, 3]),
        ]);

        $this->cart->condition([$tax1, $tax2]);

        $conditionsTotal = [
            'Tax 5%'  => 62.865,
            'Tax 10%' => 125.73,
        ];

        $conditions = $items[0]->conditions();
        $condition  = head($conditions);

        $this->assertEquals($condition->get('name'), 'Tax 10%');
        $this->assertEquals($condition->get('type'), 'tax');
        $this->assertEquals($condition->get('target'), 'subtotal');

        $conditions = $this->cart->conditions(null, false);
        $condition  = head($conditions);

        $this->assertEquals($condition->get('name'), 'Tax 5%');
        $this->assertEquals($condition->get('type'), 'tax');
        $this->assertEquals($condition->get('target'), 'subtotal');
    }

    /** @test */
    public function cart_retrieves_conditions_by_name()
    {
        $tax1     = $this->createCondition('Tax 5%', 'tax', '5%');
        $tax2     = $this->createCondition('Tax 10%', 'tax', '10%');
        $other    = $this->createCondition('Other 10%', 'other', '10%');
        $shipping = $this->createCondition('Shipping', 'shipping', '10');

        $items = $this->cart->add([
            $this->createItem('Foobar 1', 244, 3, $tax2, [3, 3]),
            $this->createItem('Foobar 2', 125, 3, $tax2, [3, 3])
        ]);

        $this->assertEquals($items[0]->total(), 825);
        $this->assertEquals($items[1]->total(), 432.3);

        $this->assertEquals($this->cart->total(), 1257.3);
        $this->assertEquals($this->cart->subtotal(), 1257.3);

        $this->cart->setConditionsOrder([
            'discount',
            'tax',
        ]);

        $this->cart->condition([$tax2, $tax1, $shipping]);

        $this->assertEquals($this->cart->total(), 1445.895);

        $conditionsOrder = [
            'discount',
            'tax',
        ];

        $this->assertEquals($this->cart->getConditionsOrder(), $conditionsOrder);

        $this->cart->setConditionsOrder([
            'discount',
            'tax',
            'shipping',
        ]);

        $this->cart->condition([$tax2, $tax1, $shipping]);

        $this->assertEquals($this->cart->total(), 1455.895);

        $conditionResults = [
            'tax' => [
                'Tax 10%' => 240.03,
                'Tax 5%'    => 62.865,
            ],
            'shipping' => [
                'Shipping' => 10.00,
            ],
        ];

        $this->assertEquals($this->cart->conditionsTotal(), $conditionResults);

        $this->cart->setConditionsOrder([
            'discount',
            'other',
            'tax',
            'shipping',
        ]);

        $this->cart->condition([$tax2, $tax1, $shipping, $other]);

        $this->assertEquals($this->cart->total(), 1600.4845);

        $conditionResults = [
            'tax' => [
                'Tax 10%' => 252.603,
                'Tax 5%'    => 69.1515,
            ],
            'other' => [
                'Other 10%' => 125.73,
            ],
            'shipping' => [
                'Shipping' => 10.00,
            ],
        ];

        $this->assertEquals($this->cart->conditionsTotal(), $conditionResults);

        $conditionResults = [
            'tax' => [
                'Tax 10%' => 138.303,
                'Tax 5%'    => 69.1515,
            ],
            'other' => [
                'Other 10%' => 125.73,
            ],
            'shipping' => [
                'Shipping' => 10.00,
            ],
        ];

        $this->assertEquals($this->cart->conditionsTotal(null, false), $conditionResults);

        $conditionResults = [
            'Tax 10%' => 252.603,
            'Tax 5%'    => 69.1515,
        ];

        $this->assertEquals($this->cart->conditionsTotal('tax'), $conditionResults);

        $conditionResults = [
            'tax' => [
                'Tax 10%' => 114.3,
            ],
        ];

        $this->assertEquals($this->cart->itemsConditionsTotal(), $conditionResults);

        $conditionResults = [
            'Tax 10%' => 114.3,
        ];

        $this->assertEquals($this->cart->itemsConditionsTotal('tax'), $conditionResults);
        $this->assertEquals($this->cart->itemsConditionsTotal('nonexisting'), []);
    }

    /** @test */
    public function cart_handles_inclusive_conditions_alone()
    {
        $tax = $this->createCondition('Tax 10%', 'tax', '10%', 'subtotal', null, true);

        $items = $this->cart->add([
            $this->createItem('Foobar 1', 100, 5, $tax),
            $this->createItem('Foobar 2', 200, 2, $tax),
        ]);

        $this->assertEquals($items[0]->subtotal(), 500);
        $this->assertEquals($items[0]->total(), 500);
        $this->assertEquals(round($items[0]->conditionsTotalSum('tax')), 45);

        $this->assertEquals($items[1]->subtotal(), 400);
        $this->assertEquals($items[1]->total(), 400);
        $this->assertEquals(round($items[1]->conditionsTotalSum('tax')), 36);

        $this->assertEquals($this->cart->subtotal(), 900);
        $this->assertEquals($this->cart->total(), 900);

        $this->cart->condition($tax);

        $this->assertEquals($this->cart->total(), 900);
        $this->assertEquals(round($this->cart->conditionsTotalSum('tax', false)), 82);
        $this->assertEquals(round($this->cart->conditionsTotalSum('tax')), 164);
    }

    /** @test */
    public function cart_handles_inclusive_conditions_with_other_conditions()
    {
        $taxInc = $this->createCondition('Tax 10% Inc', 'tax', '10%', 'subtotal', null, true);
        $taxExc = $this->createCondition('Tax 10% Exc', 'tax', '10%');

        $items = $this->cart->add([
            $this->createItem('Foobar 1', 100, 5, [$taxInc, $taxExc]),
            $this->createItem('Foobar 2', 200, 2, [$taxInc, $taxExc]),
        ]);

        $this->assertEquals($items[0]->subtotal(), 500);
        $this->assertEquals($items[0]->total(), 550);
        $this->assertEquals(round($items[0]->conditionsTotalSum('tax')), 95);

        $this->assertEquals($items[1]->subtotal(), 400);
        $this->assertEquals($items[1]->total(), 440);
        $this->assertEquals(round($items[1]->conditionsTotalSum('tax')), 76);

        $this->assertEquals($this->cart->subtotal(), 990);
        $this->assertEquals($this->cart->total(), 990);

        $this->cart->condition($taxInc);

        $this->assertEquals($this->cart->total(), 990);
        $this->assertEquals(round($this->cart->conditionsTotalSum('tax', false)), 90);
        $this->assertEquals(round($this->cart->conditionsTotalSum('tax')), 262);
    }

    /** @test */
    public function cart_calculates_total_till_condition_type()
    {
        $discount = $this->createCondition('Discount 5%', 'discount', '-5.00%');
        $other    = $this->createCondition('Other 5', 'other', 5);
        $tax      = $this->createCondition('Tax 10%', 'tax', '10%');
        $shipping = $this->createCondition('Shipping', 'shipping', '10');

        $this->cart->setItemsConditionsOrder([
            'discount',
            'other',
            'tax',
            'shipping',
        ]);

        $item = $this->createItem('Foobar 1', 100, 5, [$discount, $shipping, $other, $tax]);

        $item = $this->cart->add($item);

        $this->assertEquals($item->total('discount'), 475);
        $this->assertEquals($item->total('other'), 480);
        $this->assertEquals($item->total('tax'), 528);
        $this->assertEquals($item->total('shipping'), 538);
    }

    /** @test */
    public function cart_calculates_total_till_condition_type_per_item()
    {
        $discount = $this->createCondition('Discount 5%', 'discount', '-5.00%');
        $other    = $this->createCondition('Other 5', 'other', 5);
        $tax      = $this->createCondition('Tax 10%', 'tax', '10%');
        $shipping = $this->createCondition('Shipping', 'shipping', '10');

        $this->cart->setItemsConditionsOrder([
            'discount',
            'other',
            'tax',
            'shipping',
        ]);

        $this->cart->setConditionsOrder([
            'discount',
            'other',
            'tax',
            'shipping',
        ]);

        $item1 = $this->createItem('Foobar 1', 100, 5, [$discount, $shipping, $other, $tax]);
        $item2 = $this->createItem('Foobar 2', 100, 5, [$discount, $shipping, $other, $tax]);

        $items = $this->cart->add([$item1, $item2]);

        $item1 = $items[0];
        $item2 = $items[1];

        $item1->setConditionsOrder([
            'discount',
        ]);

        $this->assertEquals($item1->total('discount'), 475);
        $this->assertEquals($item1->total('other'), 475);
        $this->assertEquals($item1->total('tax'), 475);
        $this->assertEquals($item1->total('shipping'), 475);

        $this->assertEquals($item2->total('discount'), 475);
        $this->assertEquals($item2->total('other'), 480);
        $this->assertEquals($item2->total('tax'), 528);
        $this->assertEquals($item2->total('shipping'), 538);

        $this->cart->condition([$discount, $shipping, $other, $tax]);

        $this->assertEquals($this->cart->total('discount'), 962.35);
        $this->assertEquals($this->cart->total('other'), 967.35);
        $this->assertEquals($this->cart->total('tax'), 1064.085);
        $this->assertEquals($this->cart->total('shipping'), 1074.085);
    }
}
