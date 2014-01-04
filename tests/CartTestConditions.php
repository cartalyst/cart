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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Cart\Cart;
use Cartalyst\Conditions\Condition;
use Illuminate\Events\Dispatcher;
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
	 * Close mockery.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		m::close();
	}

	/**
	 * Setup resources and dependencies
	 */
	public function setUp()
	{
		$filesystem = new \Illuminate\Filesystem\Filesystem();
		$fileSessionHandler = new \Illuminate\Session\FileSessionHandler(
			$filesystem,
			__DIR__ . '/storage/sessions'
		);

		$store = new \Illuminate\Session\Store(
			'cartalyst_cart_session',
			$fileSessionHandler
		);

		$session = new \Cartalyst\Cart\Storage\Sessions\IlluminateSession($store);

		$dispatcher = new Dispatcher;

		$this->cart = new Cart($session, $dispatcher);
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
			'name'   => 'add10',
			'type'   => 'tax',
			'target' => 'subtotal',
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
					'conditions' => array($tax5p, $tax10p),
					'weight'	 => 21.00,
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

		// Items 1 tax check
		$item1 = $this->cart->items()->first();

		$this->assertEquals($item1->tax(), 20);

		// Items 2 tax check
		$item2 = $this->cart->items()->last();

		$this->assertEquals($item2->tax(), 31);

		// Apply 5% Global Tax
		$this->cart->condition($tax5p);

		// Cart tax
		$this->assertEquals($this->cart->tax(), 22.55);

		// All item taxes
		$this->assertEquals($this->cart->itemTaxes(), 51);

		// Cart subtotal
		$this->assertEquals($this->cart->subtotal(), 451);

		// Cart total
		$this->assertEquals($this->cart->total(), 473.55);
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
			'target' => 'price',
			'name'   => 'disc',
			'type'   => 'discount',
		));

		$disc10p->setActions(array(
			'value' => '-10.00%',
		));

		$add5p = new Condition(array(
			'target' => 'price',
		));

		$add5p->setActions(array(
			'value' => '5.00%',
		));


		$add5pCart = new Condition(array(
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

		$this->assertEquals($item1->total(), 372);


		// Second item
		$item2 = $this->cart->items()->last();
		$this->assertEquals($item2->subtotal(), 230);

		$this->assertEquals($item2->total(), 219);


		// Cart sub total
		$this->assertEquals($this->cart->subtotal(), 591);

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
		$condition = new Condition(array(
			'target' => 'price',
		));

		$condition->setActions(array(
			'value' => '5.00',
		));

		$condition->setRules(array(
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
					'conditions' => array($disc5p, $condition),
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

		$this->assertEquals($item1->total(), 726.75);

		// Discount
		$this->assertEquals($item1->discount(), -38.25);

		// Item 2
		$item2 = $this->cart->items()->last();
		$this->assertEquals($item2->subtotal(), 393);

		$this->assertEquals($item2->total(), 393);

		// Discount
		$this->assertEquals($item2->discount(), 0);

		// Cart
		$this->assertEquals($this->cart->subtotal(), 1119.75);

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
		$this->assertEquals($this->cart->discount(), -111.975);
	}


	public function testAddRemoveConditionsItems()
	{
		$condition = new Condition(array(
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

}
