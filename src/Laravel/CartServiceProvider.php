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
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Cart\Laravel;

use Cartalyst\Cart\Cart;
use Illuminate\Support\ServiceProvider;
use Cartalyst\Cart\Storage\IlluminateSession;

class CartServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->prepareResources();

        $this->registerSession();

        $this->registerCart();
    }

    /**
     * {@inheritDoc}
     */
    public function provides()
    {
        return [
            'cart',
            'cart.session',
        ];
    }

    /**
     * Prepare the package resources.
     *
     * @return void
     */
    protected function prepareResources()
    {
        $config = realpath(__DIR__.'/../config/config.php');

        $this->mergeConfigFrom($config, 'cartalyst.cart');

        $this->publishes([
            $config => config_path('cartalyst.cart.php'),
        ]);
    }

    /**
     * Register the session driver used by the Cart.
     *
     * @return void
     */
    protected function registerSession()
    {
        $this->app['cart.session'] = $this->app->share(function ($app) {
            $config = $app['config']->get('cartalyst.cart');

            return new IlluminateSession($app['session.store'], $config['instance'], $config['session_key']);
        });
    }

    /**
     * Register the Cart.
     *
     * @return void
     */
    protected function registerCart()
    {
        $this->app['cart'] = $this->app->share(function ($app) {
            $cart = new Cart($app['cart.session'], $app['events']);

            $cart->setRequiredIndexes(
                $app['config']->get('cartalyst.cart.requiredIndexes', [])
            );

            return $cart;
        });

        $this->app->alias('cart', 'Cartalyst\Cart\Cart');
    }
}
