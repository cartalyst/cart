# Changelog

### v9.0.0 - 2025-02-28

- Add Laravel 12 support

### v8.0.1 - 2024-04-27

- Add events for adding and removing metadata

### v8.0.0 - 2024-03-25

- Add Laravel 11 support

### v7.0.0 - 2023-05-08

- Add Laravel 10 support

### v6.0.0 - 2022-02-14

- Add Laravel 9, PHP 8.1 support

### v5.1.0 - 2020-12-22

- Add PHP 8 support

### v5.0.0 - 2020-09-12

- Updated for Laravel 8.

### v4.0.4 - 2020-04-20

`ADDED`

- Getter and setter to change the behaviour of the event dispatcher.

### v4.0.3 - 2020-04-18

`FIXED`

- Left over text on some Exception classes.

### v4.0.2 - 2020-04-12

`UPDATED`

- Bump Cartalyst Conditions dependency version

### v4.0.1 - 2020-04-11

`FIXED`

- Composer autoload warnings

### v4.0.0 - 2020-03-03

- Updated for Laravel 7.

### v3.0.0 - 2019-09-04

- BC Break: PHP 7.2 is the minimum required PHP version
- BC Break: Laravel 6.0 is the minimum supported Laravel version

### v2.0.5 - 2019-03-29

`FIXED`

- Issue with Laravel 5.8 events dispatching

### v2.0.4 - 2017-10-22

`ADDED`

- Support for Laravel 5.5 Package Discovery.
- Support for decimal quantities.

### v2.0.3 - 2017-02-07

`FIXED`

- Issue with price-based item conditions having the wrong value if it was a 100% discount.

### v2.0.2 - 2016-06-28

`FIXED`

- Issue with conditions having the wrong value if it was a 100% discount.

`UPDATED`

- Laravel bindings.

### v2.0.1 - 2015-07-28

`ADDED`

- `.gitattributes` and `.travis.yml` file.

`FIXED`

- Attributes check on Cart Collection.

### v2.0.0 - 2015-03-15

`REVISED`

- Added support for Laravel 5 and dropped support for Laravel 4.

### v1.1.3 - 2015-07-28

`ADDED`

- `.gitattributes` and `.travis.yml` file.

### v1.1.2 - 2015-01-31

`REVISED`

- Updated coding standards to `PSR-2`.

`REMOVED`

- Remove Laravel 5 requirement, support for Laravel 5 will come from on the `2.0` release version.

### v1.1.1 - 2014-11-17

`ADDED`

- Added the following events `cartalyst.cart.adding`, `cartalyst.cart.updating`, `cartalyst.cart.removing` and `cartalyst.cart.clearing`

### v1.1.0 - 2014-10-27

`ADDED`

- Added a `cartalyst.cart.created` event which is fired when a cart instance is initialized.

`REVISED`

- Improved the conditions validation.
- Improved the Cart MetaData feature once again to be more flexible.
- Some other minor tweaks and improvements.

`REMOVED`

- Removed the updateCart() method and it's corresponding calls, keeping the code more simplified.

### v1.0.9 - 2015-07-28

`ADDED`

- `.gitattributes` and `.travis.yml` file.

### v1.0.8 - 2015-01-31

`REVISED`

- Updated coding standards to `PSR-2`.

`REMOVED`

- Remove Laravel 5 requirement, support for Laravel 5 will come from on the `2.0` release version.

### v1.0.7 - 2014-10-19

`REVISED`

- Allow to add free items (price = 0.00) into the cart.

### v1.0.6 - 2014-10-11

`FIXED`

- Fixed a bug causing removed item conditions to be reapplied after updating the cart.

### v1.0.5 - 2014-10-05

`REVISED`

- Added a flag `(bool)` to the `price(:withAttributes)` method to return the item price + the item attributes total.

### v1.0.4 - 2014-09-23

`REVISED`

- Loosen requirements to allow the usage on Laravel 5.0.

### v1.0.3 - 2014-09-15

`REVISED`

- Minor tweak to check the condition result before applying the actions.

### v1.0.2 - 2014-09-05

`ADDED`

- Added an IoC Container alias for the Cart class.
- Added the provides() method to the Service Provider.

`REVISED`

- Unit tests improvements.

### v1.0.1 - 2014-07-24

`REVISED`

- Improved the setMetadata() method to allow old values to be merged when setting new values on an existing key.

### v1.0.0 - 2014-05-09

`INIT`

- Can add a single or multiple items to the cart.
- Can remove a single or multiples item from the cart.
- Can update a single or multiple items on the cart.
- Can update an item quantity.
- Can return all the items.
- Can check if an item exists on the cart.
- Can return information of an item.
- Can return the total of the cart.
- Can return the subtotal of the cart.
- Can return the subtotal of the items without conditions.
- Can return the total number of items that are in the cart.
- Can return the total cart weight.
- Can empty the cart.
- Can search for items that are in the cart.
- Can synchronize a collection of data with the cart.
- Can manage cart metadata. Ex.: Shipping or Billing information.
- Can get and set the cart required indexes.
- Can get and set the cart identity.
- Can apply conditions to the cart.
- Can apply conditions to items.
- Can return all the applied cart conditions.
- Can return all the applied items conditions.
- Can return all the applied conditions for specific types.
- Can get and set the cart conditions order.
- Can get and set the items conditions order.
- Can get the cart conditions total.
- Can get the cart conditions total for a specific type.
- Can get the cart conditions total sum.
- Can get the items conditions total.
- Can get the items conditions total for a specfic type.
- Can get the items conditions total sum.
- Can remove contions by id, name or type.
