### Laravel

The Cart package has optional support for Laravel 7 and it comes bundled with a Service Provider and a Facade for easy integration.

After installing the package, open your Laravel config file located at `config/app.php` and add the following lines.

In the `$providers` array add the following service provider for this package.

	'Cartalyst\Cart\Laravel\CartServiceProvider',

In the `$aliases` array add the following facade for this package.

	'Cart' => 'Cartalyst\Cart\Laravel\Facades\Cart',

#### Configuration

After installing, you can publish the package configuration file into your application by running the following command on your terminal:

	php artisan vendor:publish

This will publish the config file to `config/cartalyst.cart.php` where you can modify the package configuration.
