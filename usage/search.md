### Search

If you ever need to search the shopping cart, we've, once again, you covered!

You can use one or multiple properties to search for items in the cart

#### Cart::find()

Param  | Required  | Type  | Description
------ | --------- | ----- | -----------------------------------------------------
$data  | true      | array | Array of properties you want to search.


###### Example 1

Search for an item that has the id `foobar`

```php
Cart::find([

	'id' => 'foobar',

]);
```

###### Example 2

Search for an item thas has the name `Foo Bar` and the price `5`

```php
Cart::find([

	'name'  => 'Foo Bar',
	'price' => 5,

]);
```

###### Example 3

Search for items with the following attributes

```php
Cart::find([

	'attributes' => [

		'size' => [
			'price' => 5,
		],

	],

]);
```
