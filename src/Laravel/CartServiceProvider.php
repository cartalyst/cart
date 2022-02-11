<?php

/*
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
 * @version    6.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2022, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Cartalyst\Cart\Laravel;

use Cartalyst\Cart\Cart;
use Cartalyst\Conditions\Callback;
use Illuminate\Support\ServiceProvider;
use Cartalyst\Cart\Storage\IlluminateSession;

class CartServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->prepareResources();

        $this->registerSession();

        $this->registerCart();

        $this->registerCallbackContainer();
    }

    /**
     * {@inheritdoc}
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
        ], 'config');
    }

    /**
     * Register the session driver used by the Cart.
     *
     * @return void
     */
    protected function registerSession()
    {
        $this->app->singleton('cart.session', function ($app) {
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
        $this->app->bind('cart', function ($app) {
            $cart = new Cart($app['cart.session'], $app['events']);

            $cart->setRequiredIndexes(
                $app['config']->get('cartalyst.cart.requiredIndexes', [])
            );

            return $cart;
        });

        $this->app->alias('cart', 'Cartalyst\Cart\Cart');
    }

    /**
     * Register the container with the callback class.
     *
     * @return void
     */
    protected function registerCallbackContainer()
    {
        Callback::setContainer($this->app);
    }
}
