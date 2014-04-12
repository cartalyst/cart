<?php namespace Cartalyst\Cart\Tests;
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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Cart\Cart;
use Cartalyst\Cart\Storage\Sessions\IlluminateSession;
use Cartalyst\Conditions\Condition;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\FileSessionHandler;
use Illuminate\Session\Store;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class CartTestConditions extends PHPUnit_Framework_TestCase {

	/**
	 * Holds the cart instance.
	 *
	 * @var \Cartalyst\Cart\Cart
	 */
	protected $cart;

	/**
	 * Creates an item.
	 *
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	protected function createItem($name = 'Foobar', $price = 0, $quantity = 1, $conditions = [], $attrPrices = [0, 0])
	{
		return [
			'id'         => strtolower(str_replace(' ', '', $name)),
			'name'       => $name,
			'quantity'   => $quantity,
			'conditions' => $conditions,
			'price'      => $price,
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
	 * @param  boolean $inclusive
	 * @return \Cartalyst\Conditions\Condition
	 */
	protected function createCondition(
		$name,
		$type,
		$value,
		$target = 'subtotal',
		$rules = null,
		$inclusive = false
	)
	{
		$condition = new Condition([
			'name'   => $name,
			'type'   =>	$type,
			'target' => $target,
		]);

		if (is_array($value))
		{
			$actions = [];

			foreach ($value as $val)
			{
				$actions[]['value'] = $val;
			}

			if ($inclusive)
			{
				$actions[]['inclusive'] = true;
			}
		}
		else if ($inclusive)
		{
			$actions = [
				'value'     => $value,
				'inclusive' => true,
			];
		}
		else
		{
			$actions = [
				'value' => $value
			];
		}

		$condition->setActions($actions);

		if ($rules)
		{
			$condition->setRules([
				$rules,
			]);
		}

		return $condition;
	}

	/**
	 * Setup resources and dependencies
	 */
	public function setUp()
	{
		$sessionHandler = new FileSessionHandler(new Filesystem, __DIR__.'/storage/sessions');

		$session = new IlluminateSession(new Store('cartalyst_cart_session', $sessionHandler));

		$this->cart = new Cart('cart', $session, new Dispatcher);
	}

	/** @test */
	public function it_can_handle_all_defined_types()
	{
		$discount = $this->createCondition('Discount 5%', 'discount', '-5.00%');
		$other    = $this->createCondition('Other 5', 'other', 5, 'price');
		$tax      = $this->createCondition('Tax 10%', 'tax', '10%');

		$item = $this->createItem('Foobar 1', 100, 5, [$discount, $other, $tax]);

		$this->cart->add($item);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->applyConditions('discount'), 498.75);
		$this->assertEquals($item->subtotal(), 500);
		$this->assertEquals($item->conditionsTotalSum('discount'), -26.25);
		$this->assertEquals($item->conditionsTotalSum('tax'), 49.875);
		$this->assertEquals($item->total(), 548.625);
		$this->assertEquals(count($item->conditions()), 3);
		$this->assertEquals(count($item->conditions('discount')), 1);

		// Cart
		$this->assertEquals(count($this->cart->itemsConditions()), 3);
		$this->assertEquals(count($this->cart->itemsConditionsTotalSum('discount')), 1);
		$this->assertEquals($this->cart->itemsConditionsTotalSum('discount', false), -26.25);
		$this->assertEquals(count($this->cart->itemsConditionsTotalSum('tax')), 1);
		$this->assertEquals($this->cart->itemsConditionsTotalSum('tax'), 49.875);
		$this->assertEquals($this->cart->subtotal(), 548.625);
		$this->assertEquals($this->cart->itemsSubtotal(), 500);
		$this->assertEquals($this->cart->total(), 548.625);

		$this->cart->condition($tax);

		$this->assertEquals($this->cart->total(), 603.4875);
		$this->assertEquals(count($this->cart->conditions()), 4);
		$this->assertEquals(count($this->cart->conditions(null, false)), 1);

		// Items conditions
		$this->assertEquals($this->cart->itemsConditionsTotalSum(), 48.625);
		$this->assertEquals($this->cart->itemsConditionsTotalSum('discount'), -26.25);
		$this->assertEquals($this->cart->itemsConditionsTotalSum('other'), 25);
		$this->assertEquals($this->cart->itemsConditionsTotalSum('tax'), 49.875);

		// Cart conditions
		$this->assertEquals($this->cart->conditionsTotalSum(), 103.4875);
		$this->assertEquals($this->cart->conditionsTotalSum('discount'), -26.25);
		$this->assertEquals($this->cart->conditionsTotalSum('other'), 25);
		$this->assertEquals($this->cart->conditionsTotalSum('tax'), 104.7375);

		$this->cart->condition($discount);

		// Cart conditions
		$this->assertEquals($this->cart->conditionsTotalSum(), 73.313125);
		$this->assertEquals($this->cart->conditionsTotalSum('discount'), -53.68125);
		$this->assertEquals($this->cart->conditionsTotalSum('other'), 25);
		$this->assertEquals($this->cart->conditionsTotalSum('tax'), 101.994375);

		// Clear Item Conditions
		$item->clearConditions('tax');

		$this->assertEquals($item->total(), 498.75);

		$item->clearConditions();

		$this->assertEquals($item->total(), 500);
	}

	/** @test */
	public function testItemConditionTotals()
	{
		$discount = $this->createCondition('Discount 5%', 'discount', '-5%');
		$other1   = $this->createCondition('Add5', 'other', 5, 'price');
		$other2   = $this->createCondition('Add5%', 'other', '5%', 'price');
		$other3   = $this->createCondition('Other 10%', 'other', '10%');
		$tax      = $this->createCondition('Tax 10%', 'tax', '10%');

		$item = $this->createItem('Foobar', 100, 5, [$other1, $other2]);

		$this->cart->add($item);

		$this->cart->condition([$discount, $other3, $tax]);

		$this->assertEquals($this->cart->total(), 632.225);
	}

	/** @test */
	public function testItemTaxOnPrice()
	{
		$tax      = $this->createCondition('Tax 5%', 'tax', '5%', 'price');
		$shipping = $this->createCondition('Shipping', 'shipping', '10%');

		$item = $this->createItem('Foobar', 100, 5, $tax);

		$this->cart->add($item);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->conditionsTotalSum('tax'), 25);
		$this->assertEquals($item->total(), 525);

		$item->condition($shipping);

		$item->setConditionsOrder(['tax', 'shipping']);

		$this->assertEquals($item->total(), 577.5);

		$item->setConditionsOrder(['tax', 'other', 'discount']);

		$this->assertEquals($item->total(), 525);

		$this->cart->setItemsConditionsOrder(['tax', 'shipping']);

		$this->assertEquals($item->total(), 577.5);
	}

	/** @test */
	public function testItemConditions1()
	{
		$tax = $this->createCondition('Tax 10%', 'tax', '10%');

		$item = $this->createItem('Foobar', 100, 5, $tax);

		$this->cart->add($item);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->subtotal(), 500);
		$this->assertEquals($item->conditionsTotalSum('discount'), 0);
		$this->assertEquals($item->conditionsTotalSum('tax'), 50);
		$this->assertEquals($item->total(), 550);
		$this->assertEquals($this->cart->subtotal(), 550);
		$this->assertEquals($this->cart->total(), 550);

		$this->cart->condition($tax);

		$this->assertEquals($this->cart->total(), 605);
	}

	/** @test */
	public function testItemConditions()
	{
		$discount = $this->createCondition('Discount 10%', 'discount', '-10', 'subtotal', 'price <= 125');

		$item1 = $this->createItem('Foobar 1', 97, 1, $discount, [0, 3]);
		$item2 = $this->createItem('Foobar 2', 85, 1, $discount, [15, 0]);

		$this->cart->add([$item1, $item2]);

		$this->assertEquals($this->cart->items()->first()->total(), 90);
		$this->assertEquals($this->cart->items()->last()->total(), 90);
		$this->assertEquals($this->cart->itemsSubtotal(), 200);
		$this->assertEquals($this->cart->total(), 180.00);
	}

	/** @test */
	public function testCartCondition()
	{
		$discount = $this->createCondition('Discount 10%', 'discount', '-10%');
		$tax1  = $this->createCondition('Tax 10%', 'tax', '10%');
		$tax2  = $this->createCondition('Tax 12%', 'tax', '12%');

		$item = $this->createItem('Foobar', 125, 1, $tax1, [0, 3]);

		$this->cart->add($item);

		$this->cart->condition([$discount, $tax2]);

		$this->assertEquals($this->cart->total(), 141.9264);
		$this->assertEquals($this->cart->subtotal(), 140.8);
	}

	/** @test */
	public function testGetItemTotal()
	{
		$tax = $this->createCondition('Tax 10%', 'tax', '10%');

		$item = $this->createItem('Foobar', 125, 2, $tax, [3, 3]);

		$this->cart->add($item);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->subtotal(), 262);
		$this->assertEquals($item->total(), 288.2);
	}

	/** @test */
	public function testApplyMultipleConditionsOnItem()
	{
		$discount = $this->createCondition('Discount 5% + 2', 'discount', ['-5%', '-2']);
		$tax      = $this->createCondition('Tax 10%', 'tax', '10%');

		$item = $this->createItem('Foobar', 125, 2, [$tax, $discount], [0, 3]);

		$this->cart->add($item);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->subtotal(), 256);
		$this->assertEquals($item->total(), 265.32);
	}

	/** @test */
	public function testTaxes()
	{
		$discount = $this->createCondition('Discount 5%', 'discount', '-5%');
		$other    = $this->createCondition('Other 5', 'other', '5', 'price');
		$tax1     = $this->createCondition('Tax 10%', 'tax', '10%');
		$tax2     = $this->createCondition('Tax 5%', 'tax', '5%');

		$item1 = $this->createItem('Foobar 1', 97, 2, $tax1, [0, 3]);
		$item2 = $this->createItem('Foobar 2', 85, 2, [$discount, $other, $tax1, $tax2]);

		$this->cart->add([$item1, $item2]);

		$item1 = $this->cart->items()->first();
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item1->subtotal(), 200);
		$this->assertEquals($item1->conditionsTotalSum('tax'), 20);
		$this->assertEquals($item1->conditionsTotalSum('discount'), 0);
		$this->assertEquals($item1->total(), 220);

		$this->assertEquals($item2->subtotal(), 170);
		$this->assertEquals($item2->conditionsTotalSum('tax'), 25.65);
		$this->assertEquals($item2->conditionsTotalSum('discount'), -9);
		$this->assertEquals($item2->total(), 196.65);

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
	public function testCombinedConditionsOnItemsAndCart()
	{
		$discount = $this->createCondition('Discount 10%', 'discount', '-10%', 'price');
		$other1   = $this->createCondition('Other 5', 'other', '5%', 'price');
		$other2   = $this->createCondition('Other 5', 'other', '5%');
		$tax      = $this->createCondition('Tax 10%', 'tax', '10%');

		$item1 = $this->createItem('Foobar 1', 100, 4, $discount, [0, 3]);
		$item2 = $this->createItem('Foobar 2', 100, 2, [$discount, $other1], [15, 0]);

		$this->cart->add([$item1, $item2]);

		$item1 = $this->cart->items()->first();
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item1->subtotal(), 412);
		$this->assertEquals($item1->conditionsTotalSum('discount'), -40);
		$this->assertEquals($item1->total(), 372);

		$this->assertEquals($item2->subtotal(), 230);
		$this->assertEquals($item2->conditionsTotalSum('discount'), -20);
		$this->assertEquals($item2->total(), 219);

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
	public function testConditionOnItemPrice()
	{
		$other = $this->createCondition('Other 5', 'other', '5', 'price');
		$tax   = $this->createCondition('Tax 10%', 'tax', '10%');

		$item = $this->createItem('Foobar', 125, 3, $other, [3, 3]);

		$this->cart->add($item);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->subtotal(), 393);
		$this->assertEquals($item->total(), 408);
		$this->assertEquals($this->cart->subtotal(), 408);
		$this->assertEquals($this->cart->total(), 408);
		$this->cart->condition($tax);

		$this->assertEquals($this->cart->subtotal(), 408);
		$this->assertEquals($this->cart->total(), 448.8);
	}

	/** @test */
	public function testConditionOnItemNoMatchingRules()
	{
		$other = $this->createCondition('Other 5', 'other', '5', 'price', 'price > 200');

		$item = $this->createItem('Foobar', 125, 3, $other, [3, 3]);

		$this->cart->add($item);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->subtotal(), 393);
		$this->assertEquals($item->total(), 393);
	}

	/** @test */
	public function testMultipleConditionsWithRulesOnItems()
	{
		$other = $this->createCondition('Other 5', 'other', '5', 'price', 'price > 200');
		$tax   = $this->createCondition('Tax 10%', 'tax', '10%');

		$item1 = $this->createItem('Foobar 1', 244, 3, [$tax, $other], [3, 3]);
		$item2 = $this->createItem('Foobar 2', 125, 3, $other, [3,3]);

		$this->cart->add([$item1, $item2]);

		$item1 = $this->cart->items()->first();
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item1->subtotal(), 750);
		$this->assertEquals($item1->total(), 841.5);

		$this->assertEquals($item2->subtotal(), 393);
		$this->assertEquals($item2->total(), 393);

		$this->assertEquals($this->cart->subtotal(), 1234.5);
		$this->assertEquals($this->cart->total(), 1234.5);
	}

	/** @test */
	public function testMultipleConditionsWithDiscounts()
	{
		$discount1 = $this->createCondition('Discount 5%', 'discount', '-5%');
		$discount2 = $this->createCondition('Discount 10%', 'discount', '-10%');
		$other     = $this->createCondition('Other 5', 'other', '5', 'price', 'price > 200');

		$item1 = $this->createItem('Foobar 1', 244, 3, [$discount1, $other], [3, 3]);
		$item2 = $this->createItem('Foobar 2', 125, 3, $other, [3,3]);

		$this->cart->add([$item1, $item2]);

		$item1 = $this->cart->items()->first();
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item1->subtotal(), 750);
		$this->assertEquals($item1->conditionsTotalSum('discount'), -38.25);
		$this->assertEquals($item1->total(), 726.75);

		$this->assertEquals($item2->subtotal(), 393);
		$this->assertEquals($item2->conditionsTotalSum('other'), 0);
		$this->assertEquals($item2->total(), 393);

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
	public function testAddRemoveConditionsItems()
	{
		$tax   = $this->createCondition('Tax 10%', 'tax', '10%');
		$other = $this->createCondition('Other 5', 'other', '5', 'price');

		$item1 = $this->createItem('Foobar 1', 244, 3, [$tax, $other], [3, 3]);
		$item2 = $this->createItem('Foobar 2', 125, 3, $other, [3,3]);

		$this->cart->add([$item1, $item2]);

		$item1 = $this->cart->items()->first();
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item1->total(), 841.5);
		$this->assertEquals($item2->total(), 408);

		$this->cart->update([
			'b972ee677339f2d22f0009c1c158c703' => [
				'conditions' => $tax
			]
		]);

		$this->assertEquals($item1->total(), 825);

		// Remove conditions
		$this->cart->update([
			'6999396ebcc0a76802ca13b36d640f58' => [
				'conditions' => null
			]
		]);

		$this->assertEquals($item2->total(), 393);

		// Add two conditions
		$this->cart->update([
			'6999396ebcc0a76802ca13b36d640f58' => [
				'conditions' => [$tax, $other]
			]
		]);

		$this->assertEquals($item2->total(), 448.8);

		// Make sure conditions are still assigned after an item is updated
		$this->cart->update([
			'6999396ebcc0a76802ca13b36d640f58' => [
				'weights' => 20.00
			]
		]);

		$this->assertEquals($item2->total(), 448.8);
	}

	/** @test */
	public function testAddRemoveConditionsCart()
	{
		$tax   = $this->createCondition('Tax 10%', 'tax', '10%');
		$other = $this->createCondition('Other 10%', 'other', '10%');

		$item1 = $this->createItem('Foobar 1', 244, 3, $tax, [3, 3]);
		$item2 = $this->createItem('Foobar 2', 125, 3, null, [3,3]);

		$this->cart->add([$item1, $item2]);

		$this->cart->condition($tax);

		$this->assertEquals($this->cart->total(), 1339.8);
		$this->cart->condition([$tax, $other]);

		$this->assertEquals($this->cart->total(), 1473.78);
		$this->cart->clearConditions('tax', false);

		$this->assertEquals($this->cart->total(), 1339.8);
		$this->cart->clearConditions(null, false);

		$this->assertEquals($this->cart->total(), 1218);
	}

	/** @test */
	public function testRemoveConditions()
	{
		$tax1 = $this->createCondition('Tax 10%', 'tax', '10%');
		$tax2 = $this->createCondition('Item Tax 5%', 'tax', '5%', 'price');
		$tax3 = $this->createCondition('Item Tax 10%', 'tax', '10%', 'price');

		$item = $this->createItem('Foobar 1', 100, 3, [$tax2, $tax3]);

		$this->cart->add($item);

		$item1 = $this->cart->items()->first();

		$this->assertEquals($item1->total(), 345);
		$this->assertEquals($this->cart->total(), 345);
		$this->assertEquals($this->cart->subtotal(), 345);

		$this->cart->condition($tax1);

		$this->assertEquals($this->cart->subtotal(), 345);
		$this->assertEquals($this->cart->total(), 379.5);
		$this->assertEquals($this->cart->conditionsTotalSum('tax'), 79.5);

		$this->cart->clearConditions('tax', false);

		$this->assertEquals($this->cart->subtotal(), 345);
		$this->assertEquals($this->cart->total(), 345);
		$this->assertEquals($this->cart->conditionsTotalSum('tax'), 45);

		$this->cart->clearConditions('tax');

		$this->assertEquals($this->cart->subtotal(), 300);
		$this->assertEquals($this->cart->total(), 300);
		$this->assertEquals($this->cart->conditionsTotalSum('tax'), 0);

		$this->cart->clearConditions();

		$this->assertEquals($this->cart->subtotal(), 300);
		$this->assertEquals($this->cart->total(), 300);
		$this->assertEquals($this->cart->conditionsTotalSum('tax'), 0);
	}

	/** @test */
	public function testRetrieveDiscounts()
	{
		$discount = $this->createCondition('Discount 10%', 'discount', '-10%');

		$item1 = $this->createItem('Foobar 1', 244, 3, $discount, [3, 3]);
		$item2 = $this->createItem('Foobar 2', 125, 3, $discount, [3,3]);

		$this->cart->add([$item1, $item2]);

		$this->cart->condition($discount);

		$discounts = $this->cart->conditions('discount', false);

		$discountCondition = $discounts[0];

		$this->assertEquals($discountCondition->get('name'), 'Discount 10%');
		$this->assertEquals($discountCondition->get('type'), 'discount');
		$this->assertEquals($discountCondition->get('target'), 'subtotal');
	}

	/** @test */
	public function testRetrieveTaxes()
	{
		$tax = $this->createCondition('Tax 10%', 'tax', '10%');

		$item1 = $this->createItem('Foobar 1', 244, 3, $tax, [3, 3]);
		$item2 = $this->createItem('Foobar 2', 125, 3, $tax, [3,3]);

		$this->cart->add([$item1, $item2]);

		$this->cart->condition($tax);

		$taxes = $this->cart->conditions('tax', false);

		$taxCondition = $taxes[0];

		$this->assertEquals($taxCondition->get('name'), 'Tax 10%');
		$this->assertEquals($taxCondition->get('type'), 'tax');
		$this->assertEquals($taxCondition->get('target'), 'subtotal');
	}

	/** @test */
	public function testRetrieveConditionsTotal()
	{
		$tax1 = $this->createCondition('Tax 5%', 'tax', '5%');
		$tax2 = $this->createCondition('Tax 10%', 'tax', '10%');

		$item1 = $this->createItem('Foobar 1', 244, 3, $tax2, [3, 3]);
		$item2 = $this->createItem('Foobar 2', 125, 3, $tax2, [3,3]);

		$this->cart->add([$item1, $item2]);

		$this->cart->condition([$tax1, $tax2]);

		$conditionsTotal = [
			'Tax 10%' => 125.73,
			'Tax 5%'    => 62.865,
		];

		$this->assertEquals($this->cart->conditionsTotal('tax', false), $conditionsTotal);
	}

	/** @test */
	public function testRetrieveConditions()
	{
		$tax1 = $this->createCondition('Tax 5%', 'tax', '5%');
		$tax2 = $this->createCondition('Tax 10%', 'tax', '10%');

		$item1 = $this->createItem('Foobar 1', 244, 3, $tax2, [3, 3]);
		$item2 = $this->createItem('Foobar 2', 125, 3, $tax2, [3,3]);

		$this->cart->add([$item1, $item2]);

		$this->cart->condition([$tax1, $tax2]);

		$conditionsTotal = [
			'Tax 5%'  => 62.865,
			'Tax 10%' => 125.73,
		];

		// Item conditions
		$conditions = $this->cart->items()->first()->conditions();
		$condition  = head($conditions);

		$this->assertEquals($condition->get('name'), 'Tax 10%');
		$this->assertEquals($condition->get('type'), 'tax');
		$this->assertEquals($condition->get('target'), 'subtotal');

		// Cart conditions
		$conditions = $this->cart->conditions(null, false);
		$condition  = head($conditions);

		$this->assertEquals($condition->get('name'), 'Tax 5%');
		$this->assertEquals($condition->get('type'), 'tax');
		$this->assertEquals($condition->get('target'), 'subtotal');
	}

	/** @test */
	public function testApplyDifferentConditions()
	{
		$tax1     = $this->createCondition('Tax 5%', 'tax', '5%');
		$tax2     = $this->createCondition('Tax 10%', 'tax', '10%');
		$shipping = $this->createCondition('Shipping', 'shipping', '10');

		$item1 = $this->createItem('Foobar 1', 244, 3, $tax2, [3,3]);
		$item2 = $this->createItem('Foobar 2', 125, 3, $tax2, [3,3]);

		$this->cart->add([$item1, $item2]);

		$item1 = $this->cart->items()->first();
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item1->total(), 825);
		$this->assertEquals($item2->total(), 432.3);

		// Cart
		$this->assertEquals($this->cart->total(), 1257.3);
		$this->assertEquals($this->cart->subtotal(), 1257.3);

		// Set custom conditions order
		$this->cart->setConditionsOrder([
			'discount',
			'tax',
		]);

		$this->cart->condition([$tax2, $tax1, $shipping]);

		$this->assertEquals($this->cart->total(), 1445.895);

		// Set custom conditions order
		$this->cart->setConditionsOrder([
			'discount',
			'tax',
			'shipping',
		]);

		$this->cart->condition([$tax2, $tax1, $shipping]);

		$this->assertEquals($this->cart->total(), 1455.895);

		// Fetch total of a non existing condition
		$this->assertEquals($this->cart->conditionsTotal('nonexisting'), []);
	}

	/** @test */
	public function testRetrieveConditionsByName()
	{
		$tax1     = $this->createCondition('Tax 5%', 'tax', '5%');
		$tax2     = $this->createCondition('Tax 10%', 'tax', '10%');
		$other    = $this->createCondition('Other 10%', 'other', '10%');
		$shipping = $this->createCondition('Shipping', 'shipping', '10');

		$item1 = $this->createItem('Foobar 1', 244, 3, $tax2, [3,3]);
		$item2 = $this->createItem('Foobar 2', 125, 3, $tax2, [3,3]);

		$this->cart->add([$item1, $item2]);

		$item1 = $this->cart->items()->first();
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item1->total(), 825);
		$this->assertEquals($item2->total(), 432.3);

		$this->assertEquals($this->cart->total(), 1257.3);
		$this->assertEquals($this->cart->subtotal(), 1257.3);

		// Set custom conditions order
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

		// Set custom conditions order
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

		// Set custom conditions order
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

		// Cart conditions by type
		$conditionResults = [
			'Tax 10%' => 252.603,
			'Tax 5%'    => 69.1515,
		];

		$this->assertEquals($this->cart->conditionsTotal('tax'), $conditionResults);

		// Item conditions results
		$conditionResults = [
			'tax' => [
				'Tax 10%' => 114.3,
			],
		];

		$this->assertEquals($this->cart->itemsConditionsTotal(), $conditionResults);

		// Item conditions results by type
		$conditionResults = [
			'Tax 10%' => 114.3,
		];

		$this->assertEquals($this->cart->itemsConditionsTotal('tax'), $conditionResults);

		// Non existing condition
		$this->assertEquals($this->cart->itemsConditionsTotal('nonexisting'), []);
	}

	/** @test */
	public function testInclusiveConditions()
	{
		$tax = $this->createCondition('Tax 10%', 'tax', '10%', 'subtotal', null, true);

		$item1 = $this->createItem('Foobar 1', 100, 5, $tax);
		$item2 = $this->createItem('Foobar 2', 200, 2, $tax);

		$this->cart->add([$item1, $item2]);

		$item1 = $this->cart->items()->first();
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item1->subtotal(), 500);
		$this->assertEquals(round($item1->conditionsTotalSum('tax')), 45);
		$this->assertEquals($item1->total(), 500);

		$this->assertEquals($item2->subtotal(), 400);
		$this->assertEquals(round($item2->conditionsTotalSum('tax')), 36);
		$this->assertEquals($item2->total(), 400);

		$this->assertEquals($this->cart->subtotal(), 900);
		$this->assertEquals($this->cart->total(), 900);

		$this->cart->condition($tax);

		// Inclusive conditions will not affect the total
		$this->assertEquals($this->cart->total(), 900);

		// Excluding items tax
		$this->assertEquals(round($this->cart->conditionsTotalSum('tax', false)), 82);

		// Including items tax
		$this->assertEquals(round($this->cart->conditionsTotalSum('tax')), 164);
	}

	/** @test */
	public function testCombinedInclusiveConditions()
	{
		$taxInc = $this->createCondition('Tax 10% Inc', 'tax', '10%', 'subtotal', null, true);
		$taxExc = $this->createCondition('Tax 10% Exc', 'tax', '10%');

		$item1 = $this->createItem('Foobar 1', 100, 5, [$taxInc, $taxExc]);
		$item2 = $this->createItem('Foobar 2', 200, 2, [$taxInc, $taxExc]);

		$this->cart->add([$item1, $item2]);

		$item1 = $this->cart->items()->first();
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item1->subtotal(), 500);
		$this->assertEquals(round($item1->conditionsTotalSum('tax')), 95);
		$this->assertEquals($item1->total(), 550);

		$this->assertEquals($item2->subtotal(), 400);
		$this->assertEquals(round($item2->conditionsTotalSum('tax')), 76);
		$this->assertEquals($item2->total(), 440);

		$this->assertEquals($this->cart->subtotal(), 990);
		$this->assertEquals($this->cart->total(), 990);

		$this->cart->condition($taxInc);

		// Inclusive conditions will not affect the total
		$this->assertEquals($this->cart->total(), 990);

		// Excluding items tax
		$this->assertEquals(round($this->cart->conditionsTotalSum('tax', false)), 90);

		// Including items tax
		$this->assertEquals(round($this->cart->conditionsTotalSum('tax')), 262);
	}

}
