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

class Removing extends PHPUnit_Framework_TestCase {

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


	public function testDeleteSingleItem()
	{
		$this->cart->add(array(
			array(
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => 7,
				'price'    => 120.00,
			),
			array(
				'id'       => 'foobar2',
				'name'     => 'Foobar 2',
				'quantity' => 3,
				'price'    => 120.00,
			),
		));

		$this->assertEquals($this->cart->quantity(), 10);

		$this->assertEquals($this->cart->items()->count(), 2);

		$this->cart->remove('f53e8bcc3534788e4b4f296c1889cc99');

		$this->assertEquals($this->cart->quantity(), 3);

		$this->assertEquals($this->cart->items()->count(), 1);

		$this->assertEmpty($this->cart->find(array('f53e8bcc3534788e4b4f296c1889cc99')));
	}


	/**
	 * @expectedException \Cartalyst\Cart\Exceptions\CartItemNotFoundException
	 */
	public function testDeleteNonExistingItem()
	{
		$this->cart->remove('f53e8bcc3534788e4b4f296c1889cc99');
	}


	public function testDeleteMultipleItemsArray()
	{
		$this->cart->add(array(
			array(
				'id'       => 'foobar1',
				'name'     => 'Foobar 1',
				'quantity' => 7,
				'price'    => 120.00,
			),
			array(
				'id'       => 'foobar2',
				'name'     => 'Foobar 2',
				'quantity' => 3,
				'price'    => 120.00,
			),
		));

		$this->assertEquals($this->cart->items()->count(), 2);

		$this->cart->remove(array('f53e8bcc3534788e4b4f296c1889cc99', '2d2d8cb241842b326ce0e095dbfc4d41'));

		$this->assertEquals($this->cart->items()->count(), 0);
	}

}
