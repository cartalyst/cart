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
 * @version    1.0.9
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Cart\tests\Laravel\Facades;

use ReflectionClass;
use PHPUnit_Framework_TestCase;

class CartTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_test_it_is_a_facade()
    {
        $facade = new ReflectionClass('Illuminate\Support\Facades\Facade');

        $reflection = new ReflectionClass('Cartalyst\Cart\Laravel\Facades\Cart');

        $this->assertTrue($reflection->isSubclassOf($facade));
    }

    /** @test */
    public function it_can_test_it_is_a_facade_accessor()
    {
        $reflection = new ReflectionClass('Cartalyst\Cart\Laravel\Facades\Cart');

        $method = $reflection->getMethod('getFacadeAccessor');
        $method->setAccessible(true);

        $this->assertEquals('cart', $method->invoke(null));
    }
}
