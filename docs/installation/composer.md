# Install & Configure through Composer

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

> **Note:** The minimum-stability key is required so that you can use the package (which isn't marked as stable, yet).

Run a composer update from the command line.

	composer update

If you haven't yet, make sure to require Composer's autoload file in your app root to autoload the installed packages.

	require 'vendor/autoload.php';
