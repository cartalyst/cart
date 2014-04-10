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

class Reading extends PHPUnit_Framework_TestCase {

	/**
	 * Holds the Cart instance.
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

	/** @test */
	public function it_can_get_an_item_information()
	{
		$this->cart->add([
			[
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => 4,
				'price'    => 97.00,
			],
			[
				'id'       => 'foobar2',
				'name'     => 'Foobar 2',
				'quantity' => 2,
				'price'    => 21.00,
			],
		]);

		$this->assertEquals($this->cart->item('f53e8bcc3534788e4b4f296c1889cc99')->get('price'), 97);
	}

	/** @test */
	public function it_can_get_an_item_subtotal()
	{
		$this->cart->add([
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 4,
			'price'    => 97.00,
		]);

		$this->assertEquals($this->cart->items()->first()->subtotal(), 388);
	}

	/** @test */
	public function it_can_get_an_item_total()
	{
		$this->cart->add([
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 5,
			'price'    => 97.00,
		]);

		$this->assertEquals($this->cart->items()->first()->total(), 485);
	}

	/** @test */
	public function testItemDiscountedSubtotal()
	{

	}

	/** @test */
	public function it_can_get_an_item_quantity()
	{
		$this->cart->add([
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 4,
			'price'    => 97.00,
		]);

		$this->assertEquals($this->cart->items()->first()->quantity(), 4);
	}

	/** @test */
	public function it_can_get_an_item_weight()
	{
		$this->cart->add([
			'id'       => 'foobar1',
			'name'     => 'Foobar 1',
			'quantity' => 4,
			'price'    => 97.00,
			'weight'   => 21.00,
		]);

		$this->assertEquals($this->cart->items()->first()->weight(), 84);
	}


}
