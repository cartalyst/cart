## Upgrade Guide

Please refer to the following guides to update your Converter installation to the 1.1 version.

### From 1.0 to 1.1

Some minor changes occurred from `1.0` to `1.1`, please take a moment to read about them on the next sections.

##### Cart

**Instantiation**

When instantiating a new Cart class, the id no longer needs to be passed as it was as the first parameter:

Old

```php
$cart = new Cart('my-cart', $storage, $dispatcher);
```

New

```php
$cart = new Cart($storage, $dispatcher);
```

The id will be automatically fetched from the storage implementation that will be used on that cart instance.

**Renamed methods**

If you are using any of the methods below, please update them accordingly throughout your application code.

Rename `getIdentity()` to `getInstance()`
Rename `setIdentity()` to `setInstance()`

**Meta Data**

The Cart Meta Data was improved to allow to set metadata with key/pair values.

Old

```php
Cart::setMetaData([
	'foo' => 'bar'
]);
```

New

```php
Cart::setMetaData('foo', 'bar');
```

You can view some more examples on the [Metadata](#metadata) section.

##### Storage

**Instantiation**

When instantiating a new storage, please do note that the storage `$instance` parameter comes now in second place where previously would come last.

Old `__construct(SessionStore $session, $key = null, $instance = null)`
New `__construct(SessionStore $session, $instance = null, $key = null)`

The `$instance` parameter now comes before the `$key` parameter.

**Renamed methods**

If you are using any of the methods below, please update them accordingly throughout your application code.

Rename `identity()` to `getInstance()`
