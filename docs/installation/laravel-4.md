# Install & Configure in Laravel 4

## Composer {#composer}

Open your `composer.json` file and add the following lines:

	{
		"repositories": [
			{
				"type": "composer",
				"url": "http://packages.cartalyst.com"
			}
		],
		"require": {
			"cartalyst/cart": "1.0.*"
		},
		"minimum-stability": "dev"
	}

> **Note:** The minimum-stability key is required so that you can use the package (which isn't marked as stable, yet).

Run composer update from the command line

	composer update

## Service Provider {#service-provider}

Add the following to the list of service providers in `app/config/app.php`.

	'Cartalyst\Cart\Laravel\CartServiceProvider',


## Alias {#alias}

Add the following to the list of aliases in `app/config/app.php`.

	'Cart' => 'Cartalyst\Cart\Laravel\Facades\Cart',

## Configuration {#configuration}

After installing, you can publish the package's configuration file into you application by running the following command:

	php artisan config:publish cartalyst/cart

This will publish the config file to `app/config/packages/cartalyst/cart/config.php` where you can modify the package configuration.
