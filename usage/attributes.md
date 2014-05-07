## Attributes

Each item can have different attributes like size, color and you can even add a price to each attribute that will reflect on the final item price.


Key        | Required | Description
---------- | -------- | ---------------------------------------------------------
label      | true     | The name that is displayed to the end user.
value      | true     | The attribute value.
price      | false    | The attribute price.
weight     | false    | The attribute weight.

> **Note:** You can pass custom `key`/`value` pairs into the array, please check the examples below.

```php
Cart::add([

	'id'         => 'tshirt',
	'name'       => 'T-Shirt',
	'quantity'   => 1,
	'price'      => 12.50,
	'attributes' => [

		'size' => [
			'label' => 'Large',
			'value' => 'l',
			'price' => 5,
		],

		'color' => [
			'label' => 'Red',
			'value' => 'red',
		],

	],

]);
```
