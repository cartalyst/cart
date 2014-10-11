### Serialization

Since v1.1, the Cart package supports serialization out of the box, allowing you to store a full shopping cart with all the information or the information you only need almost where you want and to retrieve it later for display and full usage.

Please read the following sections to learn how to serialize and unserialize the cart.

#### Serializable Properties

By default when calling the `serialize();` method, we will serialize the following properties:

Property             | Description
-------------------- | ---------------------------------------------------------
items                | The items that are inside the cart.
metaData             | The meta data with custom information.
conditions           | The conditions.
conditionsOrder      | The conditions order.
requiredIndexes      | The required cart indexes.
itemsConditionsOrder | The items conditions order.

If you need to change these properties, you can use the [`setSerializable()`](#set-the-current-serializable-properties) method.

###### Get the current Serializable Properties

```php
$serializable = Cart::getSerializable();
```

###### Set the current Serializable Properties

Key        | Required | Type   | Description
---------- | -------- | ------ | ----------------------------------------------
properties | true     | array  | Array of properties that should be serializable.

```php
$properties = [
	'items',
	'conditions',
	'conditionsOrder',
	'itemsConditionsOrder',
];

$serializable = Cart::setSerializable($properties);
```

#### Serialize

Serializing the Cart is as easy as:

```
$cart = Cart::serialize();
```

#### Unserialize

Key   | Required | Type   | Description
----- | -------- | ------ | ----------------------------------------------------
$cart | true     | string | A previously serialized cart.


Unserializing is straightforward as well, all you need to provide is the serialized cart and that's it :)

```
# Using the serialized $cart from the previous example
Cart::unserialize($cart);
```
