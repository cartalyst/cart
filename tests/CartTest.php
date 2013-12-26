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
use Cartalyst\Cart\Weight;
use Illuminate\Support\Collection;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class CartTest extends PHPUnit_Framework_TestCase {

	/**
	 * Holds the cart instance.
	 * @var \Cartalyst\Cart\Cart
	 */
	protected $cart;

	/**
	 *
	 */
	public function tearDown()
	{
		m::close();
	}

	/**
	 *
	 */
	public function __construct()
	{
		$fileSystem = new \Illuminate\Filesystem\Filesystem();
		$fileSessionHanlder = new \Illuminate\Session\FileSessionHandler($fileSystem, __DIR__ . '/storage/sessions');
		$store = new \Illuminate\Session\Store('cartalyst_cart_session', $fileSessionHanlder);

		$session = new \Cartalyst\Cart\Storage\Sessions\IlluminateSession($store);

		$this->cart = new Cart($session, new Weight);
	}

	/**
	 *
	 */
	public function testcartCanBeInstantiated()
	{
		$storage = m::mock('\Cartalyst\Cart\Storage\Sessions\IlluminateSession');
		$this->cart = new Cart($storage, new Weight);
	}

	/**
	 *
	 */
	public function testCanAdd()
	{
		$cart = m::mock('cart');
		$cart->shouldReceive('add')->once();

		$cart->add();
	}

	/**
	 *
	 */
	public function testCanUpdate()
	{
		$cart = m::mock('cart');
		$cart->shouldReceive('update')->once();

		$cart->update();
	}

	/**
	 *
	 */
	public function testCanDelete()
	{
		$cart = m::mock('cart');
		$cart->shouldReceive('delete')->once();

		$cart->delete();
	}

	/**
	 *
	 */
	public function testClearCart()
	{
		$this->cart->clear();
		$this->assertEmpty($this->cart->items()->toArray());
	}

	/**
	 *
	 */
	public function testSetRequiredIndexes()
	{
		$indexes = array(
			'price',
		);

		$this->cart->setRequiredIndexes($indexes);

		$this->assertTrue(in_array('price', $this->cart->getRequiredIndexes()));
	}

	/**
	 *
	 */
	public function testAddItemToCart()
	{
		$this->cart->add(
			array(
				array(
					'id'       => 'foobar1',
					'name'     => 'Foobar 1',
					'quantity' => 2,
					'price'    => 125.00,
					'attributes' => array(
						'size'  => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 5.00,
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

		$this->assertEquals($this->cart->items()->count(), 1);
	}

	/**
	 *
	 */
	public function testAddItemsToCart()
	{
		$indexes = array(
			'price',
		);

		$this->cart->setRequiredIndexes($indexes);

		$this->cart->add(
			array(
				array(
					'id'       => 'foobar1',
					'name'     => 'Foobar 1',
					'quantity' => '02.8',
					'price'    => 4,
				),
				array(
					'id'       => 'foobar2',
					'name'     => 'Foobar 2',
					'quantity' => 2,
					'price'    => 21.00,
				),
				array(
					'id'       => 'foobar3',
					'name'     => 'Foobar 3',
					'quantity' => 2,
					'price'    => 120.00,
				),
			)
		);

		$this->cart->add(
			array(
				array(
					'id'       => 'foobar1',
					'name'     => 'Foobar 1',
					'quantity' => '02.8',
					'price'    => 4,
				),
			)
		);

		foreach ($this->cart->items() as $item) {
			$this->assertEquals($item->total(), $item->get('price') * $item->get('quantity'));
		}

		$this->assertLessThan($this->cart->items()->count(), 1);
	}

	/**
	 *
	 */
	public function testUpdateItemQuantity()
	{
		$this->cart->add(
			array(
				array(
					'id'       => 'foobar1',
					'name'     => 'Foobar 1',
					'quantity' => 3,
					'price'    => 4,
				),
			)
		);

		$this->cart->add(
			array(
				array(
					'id'       => 'foobar1',
					'name'     => 'Foobar 1',
					'quantity' => 2,
					'price'    => 4,
				),
			)
		);

		$this->assertEquals($this->cart->find(array('id' => 'foobar1'))[0]->get('quantity'), 5);
	}

	/**
	 *
	 */
	public function testMultiItems()
	{
		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 2,
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

		$subtotal = 0;

		foreach ($this->cart->items() as $item) {

			$attrPrice = 0;

			if ($attributes = $item->get('attributes'))
			{
				$attrPrice = $attributes->getTotal() * $item->get('quantity');
			}

			$subtotal += $item->get('price') * $item->get('quantity') + $attrPrice;

			$this->assertEquals($item->get('price') * $item->get('quantity') + $attrPrice, $item->subtotal());

		}

		$this->assertEquals($this->cart->subtotal(), $subtotal);

		$this->assertEquals($this->cart->total(), 256);
	}

	/**
	 *
	 */
	public function testUpdateItem()
	{
		$this->cart->add(
			array(
				array(
					'id'         => 'foobar2',
					'name'       => 'Foobar 2',
					'quantity'   => 3,
					'price'      => 120.00,
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

		$this->cart->update(array(
			'27af518595dfd39ae436c70af8a74561' => 6,
		));

		$this->assertEquals($this->cart->items()->first()->get('quantity'), 6);

	}

	/**
	 *
	 */
	public function testUpdateItems()
	{
		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 7,
					'price'      => 120.00,
					'attributes' => array(
						'size' => array(
							'label' => 'Size',
							'value' => 'L',
							'price' => 15.00,
						),
					),
				),
				array(
					'id'         => 'foobar2',
					'name'       => 'Foobar 2',
					'quantity'   => 3,
					'price'      => 120.00,
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

		$this->cart->update(array(
			'2c64e56be9013bed1a17e9156e53609b' => 3,
			'27af518595dfd39ae436c70af8a74561' => 2,
		));

		$this->assertEquals($this->cart->items()->first()->get('quantity'), 3);
		$this->assertEquals($this->cart->items()->last()->get('quantity'), 2);
		$this->assertEquals($this->cart->items()->get('27af518595dfd39ae436c70af8a74561')->get('quantity'), 2);

	}

	/**
	 *
	 */
	public function testgetConditions()
	{
		$condition = new Condition(
			array(
				'name' => 'disc',
				'type' => 'discount'
			)
		);

		$condition->setRules(array(
			'price <= 125.00',
		));

		$condition->setActions(array(
			array(
				'target' => 'subtotal',
				'value'  => '10.00',
			),
		));

		$this->cart->destroy();

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 1,
					'price'      => 97.00,
					'conditions'  => $condition,
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
					'quantity'   => 1,
					'price'      => 85.00,
					'conditions'  => $condition,
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

		$this->cart->update(array(
			'd4bbf22f5afcfc9cb8c04e0942402f8d' => 4,
			'27af518595dfd39ae436c70af8a74561' => 6,
		));

		$this->assertEquals($this->cart->items()->first()->get('quantity'), 4);
		$this->assertEquals($this->cart->items()->get('27af518595dfd39ae436c70af8a74561')->get('quantity'), 6);

		$total = 0;

		foreach ($this->cart->items() as $item) {
			$total += $item->total();
		}

		$this->assertEquals($this->cart->subtotal(), $total);

	}

	/**
	 *
	 */
	public function testCartCondition()
	{

		$condition = new Condition(
			array(
				'name' => 'tax 10%',
				'type' => 'tax',
				'target' => 'subtotal',
			)
		);

		$condition->setActions(array(
			array(
				'value'  => '10.00%',
			),
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 1,
					'conditions'  => $condition,
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

		$subtotal = 0;

		$conditionCart = new Condition(array(
			'name' => 'disc 10%',
			'type' => 'discount',
			'target' => 'subtotal',
		));

		$conditionCart->setActions(array(
			array(
				'value' => '-10.00%',
			),
		));

		$this->cart->condition($conditionCart);

		$conditionTax = new Condition(array(
			'name' => 'tax 10%',
			'type' => 'tax',
			'target' => 'subtotal',
		));

		$conditionTax->setActions(array(
			array(
				'value' => '12.00%',
			),
		));

		$item = $this->cart->items()->first();

		$this->cart->condition($conditionTax);

		$this->assertEquals($this->cart->total(), 141.9264);

		$this->assertEquals($this->cart->subtotal(), 140.8);
	}

	/**
	 *
	 */
	public function testGetItemTotal()
	{
		$condition = new Condition(
			array(
				'name' => 'tax 10%',
				'type' => 'tax',
				'target' => 'subtotal',
			)
		);

		$condition->setActions(array(
			array(
				'value'  => '10.00%',
			),
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 2,
					'conditions'  => $condition,
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

		$this->assertEquals($item->total(), 288.2);
		$this->assertEquals($item->subtotal(), 262);

	}

	/**
	 *
	 */
	public function testApplyMultipleConditionsToItem()
	{
		$taxCondition = new Condition(
			array(
				'name' => 'tax 10%',
				'type' => 'tax',
				'target' => 'subtotal',
			)
		);

		$taxCondition->setActions(array(
			array(
				'value'  => '10.00%',
			),
		));

		$discountCondition = new Condition(
			array(
				'name' => 'discount 5%',
				'type' => 'discount',
				'target' => 'subtotal',
			)
		);

		$discountCondition->setActions(array(
			array(
				'value'  => '-5.00%',
			),
			array(
				'value'  => '-2.00',
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

	/**
	 *
	 */
	public function testNumberOfItems()
	{
		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 4,
					'price'      => 97.00,
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

		$this->assertEquals($this->cart->quantity(), 6);
	}

	/**
	 *
	 */
	public function testTax()
	{

		$tax10p = new Condition(
			array(
				'name' => 'tax10',
				'type' => 'tax',
				'target' => 'subtotal',
			)
		);

		$tax10p->setActions(array(
			array(
				'value'  => '10.00%',
			),
		));

		$add5 = new Condition(
			array(
				'name' => 'add10',
				'type' => 'tax',
				'target' => 'subtotal',
			)
		);

		$add5->setActions(array(
			array(
				'value'  => '5.00',
			),
		));

		$tax5p = new Condition(
			array(
				'name' => 'tax5',
				'type' => 'tax',
				'target' => 'subtotal',
			)
		);

		$tax5p->setActions(array(
			array(
				'value'  => '5.00%',
			),
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
					'conditions' => [$tax5p, $tax10p],
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
		$tax1 = $item1->tax();
		$this->assertEquals($tax1, 20);

		// Items 2 tax check
		$item2 = $this->cart->items()->last();
		$tax2 = $item2->tax();
		$this->assertEquals($tax2, 31);

		// Weight
		$this->assertEquals($this->cart->weight(), 84);

		// Apply 5% Tax
		$this->cart->condition($tax5p);

		// Cart tax
		$this->assertEquals($this->cart->tax(), 22.55);

		// All item taxes
		$this->assertEquals($this->cart->itemTaxes(), 51);

		// Cart subtotal
		$this->assertEquals($this->cart->subtotal(), 451);

		// Cart total
		$this->assertEquals($this->cart->total(), 473.55);

		// Number of items
		$this->assertEquals($this->cart->quantity(), 4);

	}

	/**
	 *
	 */
	public function testgetConditions21()
	{

		$tax10p = new Condition(
			array(
				'target' => 'subtotal',
				'name' => 'tax10',
				'type' => 'tax'
			)
		);

		$tax10p->setActions(array(
			array(
				'value'  => '10.00%',
			),
		));

		$disc10p = new Condition(
			array(
				'target' => 'price',
				'name' => 'disc',
				'type' => 'discount',
			)
		);

		$disc10p->setActions(array(
			array(
				'value'  => '-10.00%',
			),
		));

		$add5p = new Condition(
			array(
				'target' => 'price',
			)
		);

		$add5p->setActions(array(
			array(
				'value'  => '5.00%',
			),
		));


		$add5pCart = new Condition(
			array(
				'target' => 'subtotal',
			)
		);

		$add5pCart->setActions(array(
			array(
				'value'  => '5.00%',
			),
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 4,
					'price'      => 100.00,
					'weight'	 => 21.00,
					'conditions'  => [$disc10p],
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
					'weight'	 => 45.04,
					'conditions'  => [$add5p, $disc10p],
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

		// Cart Weight
		$this->assertEquals($this->cart->weight(), 174.08);

		// Apply 10% Tax
		$this->cart->condition($tax10p);

		$this->assertEquals($this->cart->subtotal(), 591);
		$this->assertEquals($this->cart->total(), 650.1);

		// Apply +5% charge other condition ( not tax or discount )
		$this->cart->condition($add5pCart);

		$this->assertEquals($this->cart->subtotal(), 591);
		$this->assertEquals($this->cart->total(), 682.605);

	}

	/**
	 *
	 */
	public function testgetConditions11()
	{

		$tax10p = new Condition(
			array(
				'name' => 'tax10',
				'type' => 'tax',
				'target' => 'subtotal',
			)
		);

		$tax10p->setActions(array(
			array(
				'value'  => '10.00%',
			),
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 4,
					'price'      => 100.00,
					'weight'	 => 21.00,
					'conditions'  => [$tax10p],
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

		// First item
		$item1 = $this->cart->items()->first();

		$this->assertEquals($item1->subtotal(), 412);
		$this->assertEquals($item1->total(), 453.2);


	}

	/**
	 *
	 */
	public function testConditionsItemPrice()
	{
		$condition = new Condition(
			array(
				'target' => 'price',
			)
		);

		$condition->setActions(array(
			array(
				'value'  => '5.00',
			),
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar1',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'conditions'  => $condition,
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

		$tax10p = new Condition(
			array(
				'name' => 'tax10',
				'type' => 'tax',
				'target' => 'subtotal',
			)
		);

		$tax10p->setActions(array(
			array(
				'value'  => '10.00%',
			),
		));

		$this->cart->condition($tax10p);

		$this->assertEquals($this->cart->subtotal(), 408);
		$this->assertEquals($this->cart->total(), 448.8);

	}

	/**
	 *
	 */
	public function testConditionsItemNoMatchingRules()
	{
		$condition = new Condition(
			array(
				'target' => 'price',
			)
		);

		$condition->setActions(array(
			array(
				'value'  => '5.00',
			),
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
					'conditions'  => $condition,
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

		$this->assertEquals($this->cart->subtotal(), 393);
		$this->assertEquals($this->cart->total(), 393);

		$tax10p = new Condition(
			array(
				'name' => 'tax10',
				'type' => 'tax',
				'target' => 'subtotal',
			)
		);

		$tax10p->setActions(array(
			array(
				'value'  => '10.00%',
			),
		));

		$this->cart->condition($tax10p);

		$this->assertEquals($this->cart->subtotal(), 393);
		$this->assertEquals($this->cart->total(), 432.3);

	}

	/**
	 *
	 */
	public function testConditionsItemsNoMatchingRules()
	{
		$condition = new Condition(
			array(
				'target' => 'price',
			)
		);

		$condition->setActions(array(
			array(
				'value'  => '5.00',
			),
		));

		$condition->setRules(array(
			'price > 200'
		));

		$tax10p = new Condition(
			array(
				'name' => 'tax10',
				'type' => 'tax',
				'target' => 'subtotal',
			)
		);

		$tax10p->setActions(array(
			array(
				'value'  => '10.00%',
			),
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar4',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'conditions'  => [$tax10p, $condition],
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
					'conditions'  => $condition,
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

		$item1 = $this->cart->items()->first();
		$this->assertEquals($item1->subtotal(), 750);
		$this->assertEquals($item1->total(), 841.5);

		$item2 = $this->cart->items()->last();
		$this->assertEquals($item2->subtotal(), 393);
		$this->assertEquals($item2->total(), 393);

		$this->assertEquals($this->cart->subtotal(), 1234.5);
		$this->assertEquals($this->cart->total(), 1234.5);

		$tax10p = new Condition(
			array(
				'name' => 'tax10',
				'type' => 'tax',
				'target' => 'subtotal',
			)
		);

		$tax10p->setActions(array(
			array(
				'value'  => '10.00%',
			),
		));

		$this->cart->condition($tax10p);

		$this->assertEquals($this->cart->subtotal(), 1234.5);
		$this->assertEquals($this->cart->total(), 1357.95);

	}

	/**
	 *
	 */
	public function testConditionsDiscounts()
	{
		$condition = new Condition(
			array(
				'target' => 'price',
			)
		);

		$condition->setActions(array(
			array(
				'value'  => '5.00',
			),
		));

		$condition->setRules(array(
			'price > 200'
		));

		$disc5p = new Condition(
			array(
				'name' => 'disc5p',
				'type' => 'discount',
				'target' => 'subtotal',
			)
		);

		$disc5p->setActions(array(
			array(
				'value'  => '-5.00%',
			),
		));

		$this->cart->add(
			array(
				array(
					'id'         => 'foobar4',
					'name'       => 'Foobar 1',
					'quantity'   => 3,
					'conditions'  => [$disc5p, $condition],
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
					'conditions'  => $condition,
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

		// item 1
		$item1 = $this->cart->items()->first();
		$this->assertEquals($item1->subtotal(), 750);
		$this->assertEquals($item1->total(), 726.75);

		// item discount
		$this->assertEquals($item1->discount(), -38.25);

		// item 2
		$item2 = $this->cart->items()->last();
		$this->assertEquals($item2->subtotal(), 393);
		$this->assertEquals($item2->total(), 393);

		$this->assertEquals($this->cart->subtotal(), 1119.75);
		$this->assertEquals($this->cart->total(), 1119.75);

		$tax10p = new Condition(
			array(
				'name' => 'tax10',
				'type' => 'discount',
				'target' => 'subtotal',
			)
		);

		$tax10p->setActions(array(
			array(
				'value'  => '-10.00%',
			),
		));

		$this->cart->condition($tax10p);

		$this->assertEquals($this->cart->subtotal(), 1119.75);
		$this->assertEquals($this->cart->total(), 1007.775);

		// Cart discount
		$this->assertEquals($this->cart->discount(), -111.975);

	}

	/*
	|--------------------------------------------------------------------------
	| Exceptions
	|--------------------------------------------------------------------------
	|
	*/

	/**
	 * @expectedException  \Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException
	 */
	public function testThrowsCartMissingRequiredIndexException()
	{

		$this->cart->add(
			array(
				array(
					'name' => 'abc',
					'price' => 20.00,
					'quantity' => 5,
				),
			)
		);

	}

	/**
	 * @expectedException  \Cartalyst\Cart\Exceptions\CartInvalidQuantityException
	 */
	public function testThrowsCartInvalidQuantityException()
	{
		$this->cart->add(
			array(
				array(
					'id' => 1,
					'name' => 'abc',
					'price' => 20.00,
					'quantity' => 'dsdas',
				),
			)
		);

	}

	/**
	 * @expectedException  \Cartalyst\Cart\Exceptions\CartInvalidPriceException
	 */
	public function testThrowsCartInvalidPriceException()
	{
		$this->cart->add(
			array(
				array(
					'id' => 1,
					'name' => 'abc',
					'price' => 'dd',
					'quantity' => 5,
				),
			)
		);
	}

	/**
	 * @expectedException  \Cartalyst\Cart\Exceptions\CartInvalidAttributesException
	 */
	public function testThrowsCartInvalidAttributesException()
	{
		$this->cart->add(
			array(
				array(
					'id' => 1,
					'name' => 'abc',
					'price' => 20.00,
					'quantity' => 5,
					'attributes' => 'abc',
				),
			)
		);

	}

	/**
	 * @expectedException  \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function testThrowsCartItemNotFoundException()
	{
		$this->cart->remove('abc');
	}

}
