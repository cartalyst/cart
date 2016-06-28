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
 * @version    2.0.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Cart\tests;

require 'CartTestCase.php';

use Mockery as m;
use Cartalyst\Cart\Cart;
use Illuminate\Events\Dispatcher;

class CartTest extends CartTestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function cart_can_be_instantiated()
    {
        new Cart(
            m::mock('Cartalyst\Cart\Storage\IlluminateSession'),
            m::mock('Illuminate\Events\Dispatcher')
        );
    }

    /** @test */
    public function it_can_set_the_required_indexes()
    {
        $this->cart->setRequiredIndexes([
            'price',
        ]);

        $this->assertTrue(in_array('price', $this->cart->getRequiredIndexes()));
    }

    /** @test */
    public function it_can_get_the_cart_identity()
    {
        $this->assertEquals($this->cart->getInstance(), 'cart');
    }

    /** @test */
    public function it_can_set_the_cart_identity()
    {
        $this->cart->setInstance('testCart');

        $this->assertEquals($this->cart->getInstance(), 'testCart');
    }

    /** @test */
    public function it_can_get_the_cart_storage()
    {
        $this->assertInstanceOf('Cartalyst\Cart\Storage\StorageInterface', $this->cart->getStorage());
    }

    /** @test */
    public function it_can_set_the_cart_storage()
    {
        $this->cart->setStorage(
            m::mock('Cartalyst\Cart\Storage\StorageInterface')
        );

        $this->assertInstanceOf('Cartalyst\Cart\Storage\StorageInterface', $this->cart->getStorage());
    }

    /** @test */
    public function it_can_get_the_cart_dispatcher()
    {
        $this->assertInstanceOf('Illuminate\Events\Dispatcher', $this->cart->getDispatcher());
    }

    /** @test */
    public function it_can_set_the_cart_dispatcher()
    {
        $this->cart->setDispatcher(m::mock('Illuminate\Events\Dispatcher'));

        $this->assertInstanceOf('Illuminate\Events\Dispatcher', $this->cart->getDispatcher());
    }

    /** @test */
    public function it_can_get_the_total_number_of_items_inside_the_cart()
    {
        $this->cart->add([
            $this->createItem('Foobar 1', 97, 4),
            $this->createItem('Foobar 2', 85, 2),
        ]);

        $this->assertEquals($this->cart->quantity(), 6);
    }

    /** @test */
    public function it_can_get_the_total_cart_weight()
    {
        $item1 = $this->createItem('Foobar 1', 97, 4, null, null, 21.49);
        $item2 = $this->createItem('Foobar 2', 85, 2, null, null, 21.32);
        $item3 = [
            'id'         => 'foobar2',
            'name'       => 'Foobar 2',
            'quantity'   => 2,
            'price'      => 200.00,
            'weight'     => 20.00,
            'attributes' => [
                'size' => [
                    'label' => 'Large',
                    'value' => 'l',
                    'weight' => 1.5,
                ],
                'shape' => [
                    'label' => 'Circle',
                    'value' => 'c',
                    'weight' => 10.00,
                ],
            ],
        ];

        $this->cart->add([$item1, $item2, $item3]);

        $this->assertEquals($this->cart->weight(), 191.6);
    }

    /** @test */
    public function cart_can_be_cleared()
    {
        $this->cart->add(
            $this->createItem('Foobar 1', 97, 4, null, null, 21.49)
        );

        $this->assertEquals($this->cart->quantity(), 4);

        $this->cart->clear();

        $this->assertEquals($this->cart->quantity(), 0);
        $this->assertTrue($this->cart->items()->isEmpty());
    }

    /** @test */
    public function cart_can_be_searched()
    {
        $this->cart->add([
            $this->createItem('Foobar 1', 97, 2, null, [0, 17.00], 21.00),
            $this->createItem('Foobar 2', 85, 2, null, [15, 0], 21.00),
            $this->createItem('Foobar 3', 35, 5, null, [5, 17.00], 21.00),
        ]);

        $items = $this->cart->find([
            'price'    => 85,
            'quantity' => 2,
        ]);

        $this->assertEquals($items[0]->get('id'), 'foobar2');

        $this->assertCount(1, $items);
    }

    /** @test */
    public function cart_can_be_searched_by_items_attributes()
    {
        $this->cart->add([
            $this->createItem('Foobar 1', 97, 2, null, [0, 17.00], 21.00),
            $this->createItem('Foobar 2', 85, 2, null, [15, 0], 21.00),
            $this->createItem('Foobar 3', 35, 5, null, [5, 17.00], 21.00),
        ]);

        $item = $this->cart->find([
            'price'    => 85,
            'quantity' => 2,
            'weight'   => 21,
        ]);

        $this->assertEquals($item[0]->get('id'), 'foobar2');
        $this->assertEquals($item[0]->price(), 85);

        $items = $this->cart->find([
            'attributes' => [
                'color' => [
                    'price' => 17,
                ],
            ],
        ]);

        $this->assertCount(2, $items);
        $this->assertEquals($items[0]->get('id'), 'foobar1');
        $this->assertEquals($items[1]->get('id'), 'foobar3');
    }

    /** @test */
    public function cart_can_be_searched_and_returning_empty_results()
    {
        $this->cart->add([
            'id'       => 'foobar2',
            'name'     => 'Foobar 2',
            'quantity' => 2,
            'price'    => 200.00,
        ]);

        $item = $this->cart->find([
            'price' => 85,
            'attributes' => [
                'color' => [
                    'label' => 'Red',
                ],
            ],
        ]);

        $this->assertEmpty($item);
    }

    /** @test */
    public function see_if_item_exists()
    {
        $item = $this->cart->add(
            $this->createItem('Foobar 1', 200, 2)
        );

        $this->assertTrue($this->cart->exists($item['rowId']));
    }

    /** @test */
    public function see_if_item_does_not_exist()
    {
        $this->assertFalse($this->cart->exists('foobar'));
    }

    /** @test */
    public function it_can_set_meta_data()
    {
        $this->cart->setMetaData('abc', 'aaa');
    }

    /** @test */
    public function it_can_set_meta_data_with_dot_notation()
    {
        $this->cart->setMetaData('foo', 'bar');

        $this->assertEquals($this->cart->getMetaData('foo'), 'bar');


        $this->cart->setMetaData('foo', ['bar' => 'baz']);

        $this->assertEquals(count($this->cart->getMetaData('foo')), 1);
        $this->assertEquals($this->cart->getMetaData('foo.bar'), 'baz');


        $this->cart->setMetaData('foo.bat', 'qux');

        $this->assertEquals(count($this->cart->getMetaData('foo')), 2);
        $this->assertEquals($this->cart->getMetaData('foo.bar'), 'baz');
        $this->assertEquals($this->cart->getMetaData('foo.bat'), 'qux');
    }


    /** @test */
    public function it_can_retrieve_meta_data()
    {
        $this->cart->setMetaData('shipping_info', [
            'personal_details' => [
                'name' => 'John Doe',
            ],
            'billing_address' => [
                'house'  => 123,
                'street' => '123 Street.',
            ],
        ]);

        $this->assertEquals($this->cart->getMetaData('shipping_info.personal_details.name'), 'John Doe');
        $this->assertEquals($this->cart->getMetaData('shipping_info.billing_address.street'), '123 Street.');
        $this->assertEquals($this->cart->getMetaData('nonexistent', 'default'), 'default');
    }

    /** @test */
    public function it_can_remove_meta_data()
    {
        $this->cart->setMetaData('shipping_info', [
            'personal_details' => [
                'name' => 'John Doe',
            ],
            'billing_address' => [
                'house'  => 123,
                'street' => '123 Street.',
            ],
        ]);

        $this->assertEquals($this->cart->getMetaData('shipping_info.personal_details.name'), 'John Doe');

        $this->cart->removeMetaData('shipping_info.personal_details');

        $this->assertEmpty($this->cart->getMetaData('shipping_info.personal_details'));
        $this->assertEquals($this->cart->getMetaData('shipping_info.billing_address.house'), 123);

        $this->cart->removeMetaData();

        $this->assertEmpty($this->cart->getMetaData('shipping_info.personal_details'));
        $this->assertEmpty($this->cart->getMetaData('shipping_info.billing_address'));
    }

    /** @test */
    public function it_can_get_the_cart_instance_from_the_collection()
    {
        $this->assertInstanceOf('Cartalyst\Cart\Cart', $this->cart->getCart());
    }

    /** @test */
    public function it_can_serialize_and_unserialize_the_cart()
    {
        $this->cart->add([
            $this->createItem('Foobar 1', 97, 2, null, [0, 17.00], 21.00),
            $this->createItem('Foobar 2', 85, 2, null, [15, 0], 21.00),
            $this->createItem('Foobar 3', 35, 5, null, [5, 17.00], 21.00),
        ]);

        $this->cart->condition([
            $this->createCondition('Discount 5%', 'discount', '-5.00%')
        ]);

        $this->assertCount(3, $this->cart->items());
        $this->assertEquals(9, $this->cart->quantity());
        $this->assertEquals(677.35, $this->cart->total());
        $this->assertCount(1, $this->cart->conditions('discount'));

        $cart = $this->cart->serialize();

        $this->cart->clear();

        $this->assertEquals(0, $this->cart->quantity());
        $this->assertCount(0, $this->cart->conditions('discount'));

        $this->cart->unserialize($cart);

        $this->assertCount(3, $this->cart->items());
        $this->assertEquals(9, $this->cart->quantity());
        $this->assertEquals(677.35, $this->cart->total());
        $this->assertCount(1, $this->cart->conditions('discount'));
    }

    /** @test */
    public function it_can_get_the_serializable_properties()
    {
        $expected = [
            'items',
            'metaData',
            'conditions',
            'conditionsOrder',
            'requiredIndexes',
            'itemsConditionsOrder',
        ];

        $properties = $this->cart->getSerializable();

        $this->assertEquals($properties, $expected);
    }

    /** @test */
    public function it_can_set_the_serializable_properties()
    {
        $expected = [
            'items',
            'metaData',
            'conditions',
            'conditionsOrder',
            'itemsConditionsOrder',
        ];

        $this->cart->setSerializable($expected);

        $properties = $this->cart->getSerializable();

        $this->assertEquals($properties, $expected);
    }
}
