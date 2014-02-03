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
	 * Setup resources and dependencies
	 */
	public function setUp()
	{
		$sessionHandler = new FileSessionHandler(new Filesystem, __DIR__.'/storage/sessions');

		$session = new IlluminateSession(new Store('cartalyst_cart_session', $sessionHandler));

		$this->cart = new Cart('cart', $session, new Dispatcher);
	}

	public function testItemConditionAllTypes()
	{
		$add5price = new Condition(array(
			'name'   => 'Add 5 to price',
			'type'   => 'other',
			'target' => 'price',
		));

		$add5price->setActions(array(
			'value' => '5.00',
		));

		$disc5Psubtotal = new Condition(array(
			'name'   => 'Discount',
			'type'   => 'discount',
			'target' => 'subtotal',
		));

		$disc5Psubtotal->setActions(array(
			'value' => '-5.00%',
		));

		$tax10psubtotal = new Condition(array(
			'name'   => 'Tax 10%',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax10psubtotal->setActions(array(
			'value' => '10.00%',
		));

		$this->cart->add(array(
			array(
				'id'         => 'foobar1',
				'name'       => 'Foobar 1',
				'quantity'   => 5,
				'price'      => 100.00,
				'conditions' => array($add5price, $disc5Psubtotal, $tax10psubtotal),
			),
		));

		// Item 1
		$item1 = $this->cart->items()->first();

		$this->assertEquals($item1->applyConditions('discount'), 498.75);

		$this->assertEquals($item1->subtotal(), 500);

		$this->assertEquals($item1->discountsTotal(false), -26.25);

		$this->assertEquals($item1->taxesTotal(false), 49.875);

		$this->assertEquals($item1->total(), 548.625);

		// Cart
		$this->assertEquals(count($this->cart->itemsConditions()), 3);

		$this->assertEquals(count($this->cart->itemsDiscounts()), 1);

		$this->assertEquals($this->cart->itemsDiscountsTotal(), -26.25);

		$this->assertEquals(count($this->cart->itemsTaxes()), 1);

		$this->assertEquals($this->cart->itemsTaxesTotal(), 49.875);

		$this->assertEquals($this->cart->subtotal(), 548.625);

		$this->assertEquals($this->cart->itemsSubtotal(), 500);

		$this->assertEquals($this->cart->total(), 548.625);

		$this->cart->condition($tax10psubtotal);

		$this->assertEquals($this->cart->total(), 603.4875);

		// Items conditions
		$this->assertEquals($this->cart->itemsConditionsTotalSum(), 28.625);

		$this->assertEquals($this->cart->itemsConditionsTotalSum('discount'), -26.25);

		$this->assertEquals($this->cart->itemsConditionsTotalSum('other'), 5);

		$this->assertEquals($this->cart->itemsConditionsTotalSum('tax'), 49.875);

		// Cart conditions
		$this->assertEquals($this->cart->conditionsTotalSum(), 83.4875);

		$this->assertEquals($this->cart->conditionsTotalSum('discount'), -26.25);

		$this->assertEquals($this->cart->conditionsTotalSum('other'), 5);

		$this->assertEquals($this->cart->conditionsTotalSum('tax'), 104.7375);

		$this->cart->condition($disc5Psubtotal);

		$this->assertEquals($this->cart->total(), 573.313125);

		// Cart conditions
		$this->assertEquals($this->cart->conditionsTotalSum(), 53.313125);

		$this->assertEquals($this->cart->conditionsTotalSum('discount'), -53.68125);

		$this->assertEquals($this->cart->conditionsTotalSum('other'), 5);

		$this->assertEquals($this->cart->conditionsTotalSum('tax'), 101.994375);
	}

	public function testItemConditionTotals()
	{
		$add5price = new Condition(array(
			'name'   => 'Add 5 to price',
			'type'   => 'other',
			'target' => 'price',
		));

		$add5price->setActions(array(
			'value' => '5.00',
		));

		$add5pprice = new Condition(array(
			'name'   => 'Add 5% to price',
			'type'   => 'other',
			'target' => 'price',
		));

		$add5pprice->setActions(array(
			'value' => '5.00%',
		));

		$other10p = new Condition(array(
			'name'   => 'After 10%',
			'type'   => 'other',
			'target' => 'subtotal',
		));

		$other10p->setActions(array(
			'value' => '10.00%',
		));

		$disc5Psubtotal = new Condition(array(
			'name'   => 'Discount',
			'type'   => 'discount',
			'target' => 'subtotal',
		));

		$disc5Psubtotal->setActions(array(
			'value' => '-5.00%',
		));

		$disc5Psubtotal1 = new Condition(array(
			'name'   => 'Discount1',
			'type'   => 'discount',
			'target' => 'subtotal',
		));

		$disc5Psubtotal1->setActions(array(
			'value' => '-5.00%',
		));

		$tax10psubtotal = new Condition(array(
			'name'   => 'Tax 10%',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax10psubtotal->setActions(array(
			'value' => '10.00%',
		));

		$this->cart->add(array(
			array(
				'id'         => 'foobar1',
				'name'       => 'Foobar 1',
				'quantity'   => 5,
				'price'      => 100.00,
				'conditions' => array($add5price, $add5pprice),
			),
		));

		$this->cart->condition($disc5Psubtotal);
		$this->cart->condition($other10p);
		$this->cart->condition($tax10psubtotal);

		$this->assertEquals($this->cart->total(), 632.225);
	}

	public function testItemTaxOnPrice()
	{
		$add5price = new Condition(array(
			'name'   => 'Tax 5% Price',
			'target' => 'price',
			'type'   => 'tax',
		));

		$add5price->setActions(array(
			'value' => '5.00%',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 5,
					'price'      => 100.00,
					'conditions' => $add5price,
				),
			)
		);

		// Item 1
		$item1 = $this->cart->items()->first();

		// $this->assertEquals($item1->taxesTotal(false), 25);

		$this->assertEquals($item1->total(), 525);
	}

	public function testItemConditions1()
	{
		$tax10psubtotal = new Condition(array(
			'name'   => 'Tax 10%',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax10psubtotal->setActions(array(
			'value' => '10.00%',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 5,
					'price'      => 100.00,
					'conditions' => $tax10psubtotal,
				),
			)
		);

		// Item 1
		$item1 = $this->cart->items()->first();

		$this->assertEquals($item1->subtotal(), 500);

		$this->assertEquals($item1->discountsTotal(false), 0);

		$this->assertEquals($item1->taxesTotal(false), 50);

		$this->assertEquals($item1->total(), 550);

		// Cart
		$this->assertEquals($this->cart->subtotal(), 550);

		$this->assertEquals($this->cart->total(), 550);

		$this->cart->condition($tax10psubtotal);

		$this->assertEquals($this->cart->total(), 605);
	}

	public function testItemConditions()
	{
		$condition = new Condition(array(
			'name'   => 'Discount',
			'type'   => 'discount',
			'target' => 'subtotal',
		));

		$condition->setRules(array(
			'price <= 125.00',
		));

		$condition->setActions(array(
			'value' => '-10.00',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 1,
					'price'      => 97.00,
					'conditions' => $condition,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
						),
						'color' => array(
							'label' => 'Red',
							'value' => 'red',
							'price' => 3.00,
						),
					),
				),
				array(
					'id'         => 'foobar2',
					'name'       => 'Foobar 2',
					'quantity'   => 1,
					'price'      => 85.00,
					'conditions' => $condition,
					'attributes' => array(
						'size' => array(
							'label' => 'Blue',
							'value' => 'blue',
							'price' => 15.00,
						),
					),
				),
			)
		);

		$this->assertEquals($this->cart->items()->first()->total(), 90);

		$this->assertEquals($this->cart->items()->last()->total(), 90);

		$this->assertEquals($this->cart->itemsSubtotal(), 200);

		$this->assertEquals($this->cart->total(), 180.00);
	}

	public function testCartCondition()
	{
		$condition = new Condition(array(
			'name'   => 'tax 10%',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$condition->setActions(array(
			'value' => '10.00%',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 1,
					'conditions' => $condition,
					'price'      => 125.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		$conditionCart = new Condition(array(
			'name'   => 'disc 10%',
			'type'   => 'discount',
			'target' => 'subtotal',
		));

		$conditionCart->setActions(array(
			'value' => '-10.00%',
		));

		$this->cart->condition($conditionCart);

		$conditionTax = new Condition(array(
			'name'   => 'tax 10%',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$conditionTax->setActions(array(
			'value' => '12.00%',
		));

		$this->cart->condition($conditionTax);

		$this->assertEquals($this->cart->total(), 141.9264);

		$this->assertEquals($this->cart->subtotal(), 140.8);
	}

	public function testGetItemTotal()
	{
		$condition = new Condition(array(
			'name'   => 'tax 10%',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$condition->setActions(array(
			'value' => '10.00%',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 2,
					'conditions' => $condition,
					'price'      => 125.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->subtotal(), 262);

		$this->assertEquals($item->total(), 288.2);
	}

	public function testApplyMultipleConditionsOnItem()
	{
		$taxCondition = new Condition(array(
			'name'   => 'tax 10%',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$taxCondition->setActions(array(
			'value' => '10.00%',
		));

		$discountCondition = new Condition(array(
			'name'   => 'discount 5%',
			'type'   => 'discount',
			'target' => 'subtotal',
		));

		$discountCondition->setActions(array(
			array(
				'value' => '-5.00%',
			),
			array(
				'value' => '-2.00',
			),
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 2,
					'price'      => 125.00,
					'conditions' => array(
						$taxCondition,
						$discountCondition,
					),
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->subtotal(), 256);

		$this->assertEquals($item->total(), 265.32);
	}

	public function testTaxes()
	{
		$tax10p = new Condition(array(
			'name'   => 'tax10',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax10p->setActions(array(
			'value' => '10.00%',
		));

		$add5 = new Condition(array(
			'name'   => 'Add 5',
			'type'   => 'other',
			'target' => 'price',
		));

		$add5->setActions(array(
			'value'  => '5.00',
		));

		$tax5p = new Condition(array(
			'name'   => 'tax5',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax5p->setActions(array(
			'value' => '5.00%',
		));

		$disc5p = new Condition(array(
			'name'   => 'Discount 5%',
			'type'   => 'discount',
			'target' => 'subtotal',
		));

		$disc5p->setActions(array(
			'value' => '-5.00%',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 2,
					'price'      => 97.00,
					'conditions' => $tax10p,
					'weight'	 => 21.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
				array(
					'id'         => 'foobar2',
					'name'       => 'Foobar 2',
					'quantity'   => 2,
					'price'      => 85.00,
					'conditions' => array($add5, $tax10p, $tax5p, $disc5p),
					'weight'	 => 21.00,
					'attributes' => array(
						'size' => array(
							'label' => 'Size',
							'value' => 'L',
						),
					),
				),
			)
		);

		// Items 1 tax check
		$item1 = $this->cart->items()->first();

		$this->assertEquals($item1->subtotal(), 200);

		$this->assertEquals($item1->taxesTotal(false), 20);

		$this->assertEquals($item1->discountsTotal(false), 0);

		$this->assertEquals($item1->total(), 220);

		// Items 2 tax check
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item2->subtotal(), 170);

		$this->assertEquals($item2->taxesTotal(false), 25.65);

		$this->assertEquals($item2->discountsTotal(false), -9);

		$this->assertEquals($item2->total(), 196.65);

		// Apply 5% Global Tax
		$this->cart->condition($tax5p);

		// Cart sub total
		$this->assertEquals($this->cart->subtotal(), 416.65);

		// Items subtotal
		$this->assertEquals($this->cart->itemsSubtotal(), 370);

		// Cart total
		$this->assertEquals($this->cart->total(), 437.4825);

		// Cart tax without items
		$this->assertEquals($this->cart->taxesTotal(false), 20.8325);

		// Cart tax with items
		$this->assertEquals($this->cart->taxesTotal(), 66.4825);

		// Cart discount with items
		$this->assertEquals($this->cart->discountsTotal(), -9);

		// All item taxes
		$this->assertEquals($this->cart->itemsTaxesTotal(), 45.65);
	}

	public function testCombinedConditionsOnItemsAndCart()
	{
		$tax10p = new Condition(array(
			'target' => 'subtotal',
			'name'   => 'tax10',
			'type'   => 'tax'
		));

		$tax10p->setActions(array(
			'value' => '10.00%',
		));

		$disc10p = new Condition(array(
			'name'   => 'disc',
			'type'   => 'discount',
			'target' => 'price',
		));

		$disc10p->setActions(array(
			'value' => '-10.00%',
		));

		$add5p = new Condition(array(
			'name'   => 'other5',
			'type'   => 'other',
			'target' => 'price',
		));

		$add5p->setActions(array(
			'value' => '5.00%',
		));

		$add5pCart = new Condition(array(
			'name'   => 'add5cart',
			'type'   => 'other',
			'target' => 'subtotal',
		));

		$add5pCart->setActions(array(
			'value' => '5.00%',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 4,
					'price'      => 100.00,
					'weight'     => 21.00,
					'conditions' => $disc10p,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
				array(
					'id'         => 'foobar2',
					'name'       => 'Foobar 2',
					'quantity'   => 2,
					'price'      => 100.00,
					'weight'     => 45.04,
					'conditions' => array($add5p, $disc10p),
					'attributes' => array(
						'size' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 15.00,
						),
					),
				),
			)
		);

		// First item
		$item1 = $this->cart->items()->first();

		$this->assertEquals($item1->subtotal(), 412);

		$this->assertEquals($item1->discountsTotal(false), -40);

		$this->assertEquals($item1->total(), 372);

		// Second item
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item2->subtotal(), 230);

		$this->assertEquals($item2->total(), 219);

		// Cart sub total
		$this->assertEquals($this->cart->subtotal(), 591);

		// Items subtotal
		$this->assertEquals($this->cart->itemsSubtotal(), 642);

		// Cart total ( No conditions applied yet )
		$this->assertEquals($this->cart->total(), 591);

		// Apply 10% Tax
		$this->cart->condition($tax10p);

		$this->assertEquals($this->cart->subtotal(), 591);

		$this->assertEquals($this->cart->total(), 650.1);

		// Apply +5% charge other condition ( not tax or discount )
		$this->cart->condition($add5pCart);

		$this->assertEquals($this->cart->subtotal(), 591);

		$this->assertEquals($this->cart->total(), 682.605);
	}

	public function testConditionOnItemPrice()
	{
		$condition = new Condition(array(
			'name'   => 'add5price',
			'type'   => 'other',
			'target' => 'price',
		));

		$condition->setActions(array(
			'value' => '5.00',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'conditions' => $condition,
					'price'      => 125.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->subtotal(), 393);

		$this->assertEquals($item->total(), 408);

		$this->assertEquals($this->cart->subtotal(), 408);

		$this->assertEquals($this->cart->total(), 408);

		$tax10p = new Condition(array(
			'name'   => 'tax10',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax10p->setActions(array(
			'value' => '10.00%',
		));

		$this->cart->condition($tax10p);

		$this->assertEquals($this->cart->subtotal(), 408);

		$this->assertEquals($this->cart->total(), 448.8);
	}

	public function testConditionOnItemNoMatchingRules()
	{
		$condition = new Condition(array(
			'target' => 'price',
		));

		$condition->setActions(array(
			'value' => '5.00',
		));

		$condition->setRules(array(
			'price > 200'
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'conditions' => $condition,
					'price'      => 125.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		$item = $this->cart->items()->first();

		$this->assertEquals($item->subtotal(), 393);

		$this->assertEquals($item->total(), 393);
	}

	public function testMultipleConditionsWithRulesOnItems()
	{
		$condition = new Condition(array(
			'name'   => 'add5price',
			'type'   => 'other',
			'target' => 'price',
		));

		$condition->setActions(array(
			'value' => '5.00',
		));

		$condition->setRules(array(
			'price > 200'
		));


		$tax10p = new Condition(array(
			'name'   => 'tax10',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax10p->setActions(array(
			'value' => '10.00%',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar4',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'conditions' => array($tax10p, $condition),
					'price'      => 244.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),

				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'conditions' => $condition,
					'price'      => 125.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		// Item 1
		$item1 = $this->cart->items()->first();

		$this->assertEquals($item1->subtotal(), 750);

		$this->assertEquals($item1->total(), 841.5);

		// Item 2
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item2->subtotal(), 393);

		$this->assertEquals($item2->total(), 393);

		// Cart
		$this->assertEquals($this->cart->subtotal(), 1234.5);

		$this->assertEquals($this->cart->total(), 1234.5);
	}

	public function testMultipleConditionsWithDiscounts()
	{
		$add5ToPrice = new Condition(array(
			'name'   => 'add5price',
			'type'   => 'other',
			'target' => 'price',
		));

		$add5ToPrice->setActions(array(
			'value' => '5.00',
		));

		$add5ToPrice->setRules(array(
			'price > 200'
		));

		$disc5p = new Condition(array(
			'name'   => 'disc5p',
			'type'   => 'discount',
			'target' => 'subtotal',
		));

		$disc5p->setActions(array(
			'value' => '-5.00%',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar4',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'conditions' => array($disc5p, $add5ToPrice),
					'price'      => 244.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),

				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'conditions' => $add5ToPrice,
					'price'      => 125.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		// Item 1
		$item1 = $this->cart->items()->first();

		$this->assertEquals($item1->subtotal(), 750);

		$this->assertEquals($item1->discountsTotal(false), -38.25);

		$this->assertEquals($item1->total(), 726.75);

		// Item 2
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item2->subtotal(), 393);

		$this->assertEquals($item2->total(), 393);

		// Cart
		$this->assertEquals($this->cart->subtotal(), 1119.75);

		$this->assertEquals($this->cart->itemsSubtotal(), 1143);

		$this->assertEquals($this->cart->total(), 1119.75);

		$discount10p = new Condition(array(
			'name'   => 'discount10',
			'type'   => 'discount',
			'target' => 'subtotal',
		));

		$discount10p->setActions(array(
			'value' => '-10.00%',
		));

		$this->cart->condition($discount10p);

		// Cart
		$this->assertEquals($this->cart->subtotal(), 1119.75);

		$this->assertEquals($this->cart->total(), 1007.775);

		// Cart discount
		$this->assertEquals($this->cart->discountsTotal(false), -111.975);

		// Cart discount with items
		$this->assertEquals($this->cart->discountsTotal(), -150.225);
	}

	public function testAddRemoveConditionsItems()
	{
		$condition = new Condition(array(
			'name'   => 'add5price',
			'type'   => 'other',
			'target' => 'price',
		));

		$condition->setActions(array(
			'value' => '5.00',
		));

		$tax10p = new Condition(array(
			'name'   => 'tax10',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax10p->setActions(array(
			'value' => '10.00%',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar4',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'conditions' => array($tax10p, $condition),
					'price'      => 244.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),

				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'conditions' => $condition,
					'price'      => 125.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		// Item 1
		$item1 = $this->cart->items()->first();

		$this->assertEquals($item1->total(), 841.5);

		$this->cart->update(array(
			'94c52868c7af27c86c337cf4a026db40' => array(
				'conditions' => $tax10p
			)
		));

		$this->assertEquals($item1->total(), 825);

		// Item 2
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item2->total(), 408);

		// Remove conditions
		$this->cart->update(array(
			'194e85f089d754cc4759da6657840f8a' => array(
				'conditions' => null
			)
		));

		$this->assertEquals($item2->total(), 393);

		// Add two conditions
		$this->cart->update(array(
			'194e85f089d754cc4759da6657840f8a' => array(
				'conditions' => array($tax10p, $condition)
			)
		));

		$this->assertEquals($item2->total(), 448.8);

		// Make sure conditions are still assigned after an item is updated
		$this->cart->update(array(
			'194e85f089d754cc4759da6657840f8a' => array(
				'weights' => 20.00
			)
		));

		$this->assertEquals($item2->total(), 448.8);
	}

	public function testAddRemoveConditionsCart()
	{
		$tax10p = new Condition(array(
			'name'   => 'tax10',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax10p->setActions(array(
			'value' => '10.00%',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar4',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'conditions' => $tax10p,
					'price'      => 244.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),

				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'price'      => 125.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		$this->cart->condition($tax10p);

		$this->assertEquals($this->cart->total(), 1339.8);

		$this->cart->clearConditions();

		$this->assertEquals($this->cart->total(), 1218);
	}

	public function testRetrieveDiscounts()
	{
		$disc10p = new Condition(array(
			'name'   => 'disc10',
			'type'   => 'discount',
			'target' => 'subtotal',
		));

		$disc10p->setActions(array(
			'value' => '-10.00%',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar4',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'price'      => 244.00,
					'conditions' => $disc10p,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),

				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'price'      => 125.00,
					'conditions' => $disc10p,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		$this->cart->condition($disc10p);

		$discounts = $this->cart->discounts();

		$discountCondition = $discounts[0];

		$this->assertEquals($discountCondition->get('name'), 'disc10');
		$this->assertEquals($discountCondition->get('type'), 'discount');
		$this->assertEquals($discountCondition->get('target'), 'subtotal');
	}

	public function testRetrieveTaxes()
	{
		$tax10p = new Condition(array(
			'name'   => 'tax10',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax10p->setActions(array(
			'value' => '10.00%',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar4',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'price'      => 244.00,
					'conditions' => $tax10p,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),

				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'price'      => 125.00,
					'conditions' => $tax10p,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		$this->cart->condition($tax10p);

		$taxes = $this->cart->taxes();

		$taxCondition = $taxes[0];

		$this->assertEquals($taxCondition->get('name'), 'tax10');
		$this->assertEquals($taxCondition->get('type'), 'tax');
		$this->assertEquals($taxCondition->get('target'), 'subtotal');
	}


	public function testRetrieveConditionsTotal()
	{
		$tax10p = new Condition(array(
			'name'   => 'tax10',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax10p->setActions(array(
			'value' => '10.00%',
		));

		$tax5p = new Condition(array(
			'name'   => 'tax5',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax5p->setActions(array(
			'value' => '5.00%',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar4',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'price'      => 244.00,
					'conditions' => $tax10p,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),

				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'price'      => 125.00,
					'conditions' => $tax10p,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		$this->cart->condition(array($tax10p, $tax5p));

		$conditionsTotal = array(
			'tax10' => 125.73,
			'tax5'  => 62.865,
		);

		$this->assertEquals($this->cart->conditionsTotal('tax', false), $conditionsTotal);
	}


	public function testRetrieveConditions()
	{
		$tax10p = new Condition(array(
			'name'   => 'tax10',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax10p->setActions(array(
			'value' => '10.00%',
		));

		$tax5p = new Condition(array(
			'name'   => 'tax5',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax5p->setActions(array(
			'value' => '5.00%',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar4',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'price'      => 244.00,
					'conditions' => $tax10p,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),

				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'price'      => 125.00,
					'conditions' => $tax10p,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		$this->cart->condition($tax10p);

		$this->cart->condition($tax5p);

		$conditionsTotal = array(
			'tax10' => 125.73,
			'tax5'  => 62.865,
		);

		// Item conditions
		$conditions = $this->cart->items()->first()->conditions();

		$condition = head($conditions);

		$this->assertEquals($condition->get('name'), 'tax10');
		$this->assertEquals($condition->get('type'), 'tax');
		$this->assertEquals($condition->get('target'), 'subtotal');

		// Cart conditions
		$conditions = $this->cart->conditions();

		$condition = head($conditions);

		$this->assertEquals($condition->get('name'), 'tax10');
		$this->assertEquals($condition->get('type'), 'tax');
		$this->assertEquals($condition->get('target'), 'subtotal');
	}


	public function testApplyDifferentConditions()
	{
		$tax10p = new Condition(array(
			'name'   => 'tax10',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax10p->setActions(array(
			'value' => '10.00%',
		));

		$tax5p = new Condition(array(
			'name'   => 'tax5',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax5p->setActions(array(
			'value' => '5.00%',
		));

		$shipping = new Condition(array(
			'name'   => 'shipping10',
			'type'   => 'shipping',
			'target' => 'subtotal',
		));

		$shipping->setActions(array(
			'value' => '10.00',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar4',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'price'      => 244.00,
					'conditions' => $tax10p,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'price'      => 125.00,
					'conditions' => $tax10p,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		// Item 1
		$item1 = $this->cart->items()->first();

		$this->assertEquals($item1->total(), 825);

		// Item 2
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item2->total(), 432.3);

		// Cart
		$this->assertEquals($this->cart->total(), 1257.3);

		$this->assertEquals($this->cart->subtotal(), 1257.3);

		// Set custom conditions order
		$this->cart->setConditionsOrder(array(
			'discount',
			'tax',
		));

		$this->cart->condition(array($tax10p, $tax5p, $shipping));

		$this->assertEquals($this->cart->total(), 1445.895);

		// Set custom conditions order
		$this->cart->setConditionsOrder(array(
			'discount',
			'tax',
			'shipping',
		));

		$this->cart->condition(array($tax10p, $tax5p, $shipping));

		$this->assertEquals($this->cart->total(), 1455.895);
	}


	public function testRetrieveConditionsByName()
	{
		$tax10p = new Condition(array(
			'name'   => 'tax10',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax10p->setActions(array(
			'value' => '10.00%',
		));

		$tax5p = new Condition(array(
			'name'   => 'tax5',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$tax5p->setActions(array(
			'value' => '5.00%',
		));

		$other10p = new Condition(array(
			'name'   => 'other10',
			'type'   => 'other',
			'target' => 'subtotal',
		));

		$other10p->setActions(array(
			'value' => '10.00%',
		));

		$shipping = new Condition(array(
			'name'   => 'shipping10',
			'type'   => 'shipping',
			'target' => 'subtotal',
		));

		$shipping->setActions(array(
			'value' => '10.00',
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar4',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'price'      => 244.00,
					'conditions' => $tax10p,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'price'      => 125.00,
					'conditions' => $tax10p,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
						'color' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 3.00,
						),
					),
				),
			)
		);

		// Item 1
		$item1 = $this->cart->items()->first();

		$this->assertEquals($item1->total(), 825);

		// Item 2
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item2->total(), 432.3);

		// Cart
		$this->assertEquals($this->cart->total(), 1257.3);

		$this->assertEquals($this->cart->subtotal(), 1257.3);

		// Set custom conditions order
		$this->cart->setConditionsOrder(array(
			'discount',
			'tax',
		));

		$this->cart->condition(array($tax10p, $tax5p, $shipping));

		$this->assertEquals($this->cart->total(), 1445.895);

		$conditionsOrder = array(
			'discount',
			'tax',
		);

		$this->assertEquals($this->cart->getConditionsOrder(), $conditionsOrder);

		// Set custom conditions order
		$this->cart->setConditionsOrder(array(
			'discount',
			'tax',
			'shipping',
		));

		$this->cart->condition(array($tax10p, $tax5p, $shipping));

		$this->assertEquals($this->cart->total(), 1455.895);

		$conditionResults = array(
			'tax' => array(
				'tax10' => 240.03,
				'tax5'  => 62.865,
			),
			'shipping' => array(
				'shipping10' => 10.00
			),
		);

		$this->assertEquals($this->cart->conditionsTotal(), $conditionResults);

		// Set custom conditions order
		$this->cart->setConditionsOrder(array(
			'discount',
			'other',
			'tax',
			'shipping',
		));

		$this->cart->condition(array($tax10p, $tax5p, $shipping, $other10p));

		$this->assertEquals($this->cart->total(), 1600.4845);

		$conditionResults = array(
			'tax' => array(
				'tax10' => 252.603,
				'tax5'  => 69.1515,
			),
			'other' => array(
				'other10' => 125.73,
			),
			'shipping' => array(
				'shipping10' => 10.00,
			),
		);

		$this->assertEquals($this->cart->conditionsTotal(), $conditionResults);

		$conditionResults = array(
			'tax' => array(
				'tax10' => 138.303,
				'tax5'  => 69.1515,
			),
			'other' => array(
				'other10' => 125.73,
			),
			'shipping' => array(
				'shipping10' => 10.00,
			),
		);

		$this->assertEquals($this->cart->conditionsTotal(null, false), $conditionResults);

		// Cart conditions by type
		$conditionResults = array(
			'tax10' => 252.603,
			'tax5'  => 69.1515,
		);

		$this->assertEquals($this->cart->conditionsTotal('tax'), $conditionResults);

		// Item conditions results
		$conditionResults = array(
			'tax' => array(
				'tax10' => 114.3,
			),
		);

		$this->assertEquals($this->cart->itemsConditionsTotal(), $conditionResults);

		// Item conditions results by type
		$conditionResults = array(
			'tax10' => 114.3,
		);

		$this->assertEquals($this->cart->itemsConditionsTotal('tax'), $conditionResults);

	}

}
