<?php namespace Cartalyst\Cart\Tests\Crud;
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
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\FileSessionHandler;
use Illuminate\Session\Store;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class Updating extends PHPUnit_Framework_TestCase {

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
		$sessionHandler = new FileSessionHandler(new Filesystem, __DIR__ . '/storage/sessions');

		$session = new IlluminateSession(new Store('cartalyst_cart_session', $sessionHandler));

		$this->cart = new Cart('cart', $session, new Dispatcher);
	}

	/**
	 * @test
	 */
	public function it_can_update_an_item_quantity()
	{
		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 5,
			'price'    => 10.00,
		));

		$item = $this->cart->items()->first();

		$this->assertEquals($item->get('quantity'), 5);

		$this->cart->update('f53e8bcc3534788e4b4f296c1889cc99', 2);

		$this->assertEquals($item->get('quantity'), 2);
	}

	/**
	 * @test
	 */
	public function it_can_remove_an_item_with_negative_quantity_test_1()
	{
		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 5,
			'price'    => 10.00,
		));

		$item = $this->cart->items()->first();

		$this->assertEquals($item->get('quantity'), 5);

		$this->cart->update('f53e8bcc3534788e4b4f296c1889cc99', -1);

		$this->assertEquals($this->cart->quantity(), 0);
	}

	/**
	 * @test
	 */
	public function it_can_remove_an_item_with_negative_quantity_test_2()
	{
		$this->cart->add(array(
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 5,
			'price'    => 10.00,
		));

		$item = $this->cart->items()->first();

		$this->assertEquals($item->get('quantity'), 5);

		$this->cart->update('f53e8bcc3534788e4b4f296c1889cc99', array(
			'quantity' => -1,
		));

		$this->assertEquals($this->cart->quantity(), 0);
	}

	/**
	 * @test
	 */
	public function it_can_update_an_item_attributes()
	{
		$this->cart->add(array(
			'id'         => 'foobar2',
			'name'       => 'Foobar 2',
			'quantity'   => 3,
			'price'      => 120.00,
			'attributes' => array(
				'size' => array(
					'label' => 'Large',
					'value' => 'l',
					'price' => 15.00,
				),
			),
		));

		$item = $this->cart->items()->first();

		$this->assertEquals($item->get('quantity'), 3);

		$this->assertEquals($item->get('name'), 'Foobar 2');

		$this->assertEquals($item->get('attributes')->first()->get('value'), 'l');

		$this->cart->update('bbf24530f06f8f7cfcc6cc843d42b89d', array(
			'name'       => 'Foo',
			'quantity'   => 6,
			'attributes' => array(
				'size' => array(
					'label' => 'Medium',
					'value' => 'm',
					'price' => 15.00,
				),
			),
		));

		$this->assertEquals($item->get('quantity'), 6);

		$this->assertEquals($item->get('name'), 'Foo');

		$this->assertEquals($item->get('attributes')->first()->get('value'), 'm');
	}

	/**
	 * @test
	 */
	public function it_can_update_multiple_items_quantity_and_prices()
	{
		$this->cart->add(array(
			array(
				'id'         => 'foobar1',
				'name'       => 'Foobar 1',
				'quantity'   => 7,
				'price'      => 120.00,
				'attributes' => array(
					'size' => array(
						'label' => 'Large',
						'value' => 'l',
						'price' => 15.00,
					),
				),
			),
			array(
				'id'         => 'foobar2',
				'name'       => 'Foobar 2',
				'quantity'   => 3,
				'price'      => 150.00,
				'attributes' => array(
					'size' => array(
						'label' => 'Large',
						'value' => 'l',
						'price' => 15.00,
					),
				),
			),
		));

		$item1 = $this->cart->items()->first();

		$item2 = $this->cart->items()->last();

		$this->assertEquals($item1->get('quantity'), 7);

		$this->assertEquals($item2->get('quantity'), 3);

		$this->assertEquals($item1->get('price'), 120.00);

		$this->assertEquals($item2->get('price'), 150.00);

		$this->cart->update(array(
			'f8e43dd23ee6965c3c5541a54e12c64d' => array(
				'price'    => 20.00,
				'quantity' => 3,
			),
			'bbf24530f06f8f7cfcc6cc843d42b89d' => array(
				'price'    => 25.00,
				'quantity' => 2,
			),
		));

		$this->assertEquals($item1->get('quantity'), 3);

		$this->assertEquals($item2->get('quantity'), 2);

		$this->assertEquals($item1->get('price'), 20.00);

		$this->assertEquals($item2->get('price'), 25.00);
	}

}
