## Install & Configure in Laravel 4

### 1. Composer {#composer}

---

Open your `composer.json` file and add the following lines:

	{
		"require": {
			"cartalyst/cart": "1.0.*"
		},
		"repositories": [
			{
				"type": "composer",
				"url": "http://packages.cartalyst.com"
			}
		],
		"minimum-stability": "dev"
	}

> **Note:** The minimum-stability key is needed so that you can use the package (which isn't marked as stable, yet).

Run a composer update from the command line.

	php composer.phar update

### 2. Service Provider {#service-provider}

---

Add the following to the list of service providers in `app/config/app.php`.

	'Cartalyst\Cart\ServicesProvider\Laravel',

### 3. Alias {#alias}

---

Add the following to the to the list of class aliases in `app/config/app.php`.

	'Cart' => 'Cartalyst\Cart\Facades\Laravel',

### 4. Configuration {#configuration}

---

After installing, you can publish the package's configuration file into you application by running the following command:

	php artisan config:publish cartalyst/cart

This will publish the config file to `app/config/packages/cartalyst/cart/config.php` where you can modify the package configuration.
