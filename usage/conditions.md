## Conditions

The Cart package utilizes the Cartalyst Conditions package to manage item and cart based conditions.

The Cart has a default set of three condition types: `discount`, `tax` and `other` conditions.

### Applying Conditions

#### Cart Conditions

You can pass an array containing one or multiple conditions to the cart.

**Apply a single condition**

```php
$condition = new Condition([
	'name'   => 'VAT (12.5%)',
	'type'   => 'tax',
	'target' => 'subtotal',
]);

$condition->setActions([

	[
		'value' => '12.5%',
	],

]);

Cart::condition($condition);
```

**Apply multiple conditions**

```php
$conditionTax = new Condition([
	'name'   => 'VAT (12.5%)',
	'type'   => 'tax',
	'target' => 'subtotal',
]);

$conditionTax->setActions([

	[
		'value' => '12.5%',
	],

]);

$conditionDiscount = new Condition([
	'name'   => 'Discount (2.5%)',
	'type'   => 'discount',
	'target' => 'subtotal',
]);

$conditionDiscount->setActions([

	[
		'value' => '-2.5%',
	],

]);

Cart::condition([$conditionTax, $conditionDiscount]);
```

#### Item Conditions

You can add one or more (array) conditions to an item that will be assigned automatically when adding or updating items on the cart.

```php
$condition = new Condition([
	'name'   => 'VAT (12.5%)',
	'type'   => 'tax',
	'target' => 'subtotal',
]);

Cart::add([
	'id'         => 'tshirt',
	'name'       => 'T-Shirt',
	'quantity'   => 1,
	'price'      => 12.50,
	'conditions' => $condition,
]);
```

### Removing Conditions

There will be times you'll need to remove conditions, in this section we'll cover how to remove specific conditions or all the conditions at once.

### Cart::removeConditionByName()

Removes a condition by its name.

Param         | Required | Type    | Default | Description
------------- | -------- | ------- | ------- | ------------------------------------------
$name         | true     | string  | null    | The condition name.
$includeItems | false    | boolean | true    | Flag to either remove the condition from the items or not.

```php
Cart::removeConditionByName('Tax 10%');
```

### Cart::removeConditionByType()

Removes a condition by its type.

Param         | Required | Type    | Default | Description
------------- | -------- | ------- | ------- | ------------------------------------------
$type         | true     | string  | null    | The condition type.
$includeItems | false    | boolean | true    | Flag to either remove the condition from the items or not.

```php
Cart::removeConditionByType('tax');
```

### Cart::removeConditions()

Removes all conditions with the given identifier.

Param         | Required | Type    | Default | Description
------------- | -------- | ------- | ------- | ------------------------------------------
$id           | false    | mixed   | null    | The condition identifier.
$includeItems | false    | boolean | true    | Flag to either remove the condition from the items or not.
$target       | false    | string  | type    | The target that the condition was applied on.

**Remove the condition with the type `tax`.**

```php
Cart::removeConditions('tax');
```

**Remove all the applied conditions**

```php
Cart::removeConditions();
```

### Conditions Types

Conditions types are defined by the type property on conditions.

Cart handles discount, other and tax types by default.

> **Note:** If you need to define custom condition types, make sure you set the conditions order using ```Cart::setConditionsOrder($types)``` by passing it an array of types that should be handled by the cart, otherwise only default condition types will be applied.

#### Tax

Tax conditions must have the type set to tax

```php
$condition = new Condition([
	'name'   => 'VAT (12.5%)',
	'type'   => 'tax',
	'target' => 'subtotal',
]);

$condition->setActions([

	[
		'value' => '12.5%',
	],

]);
```

#### Discount

Discount conditions must have the type set to discount

```php
$condition = new Condition([
	'name'   => 'Discount (5%)',
	'type'   => 'discount',
	'target' => 'subtotal',
]);

$condition->setActions([

	[
		'value' => '-5%',
	],

]);
```

The condition above will apply a 5% discount.

#### Other

Other conditions must have the type set to other

The condition below will add 5 to the subtotal after applying discounts (if any)
assuming conditions order are set to their default order.

```php
$condition = new Condition([
	'name'   => 'Other (5%)',
	'type'   => 'other',
	'target' => 'subtotal',
]);

$condition->setActions([

	[
		'value' => '5',
	],

]);
```

### Inclusive Conditions

Inclusive conditions are not added to the total but allow you to reverse
calculate taxes that are already included in your price.

This condition will be reverse calculated and will show up on total
conditions methods, but it will not be added to the cart total.

```php
$condition = new Condition([
	'name'   => 'Tax (5%)',
	'type'   => 'tax',
	'target' => 'subtotal',
]);

$condition->setActions([

	[
		'value'     => '5%',
		'inclusive' => true,
	],

]);
```

### Conditions Order

#### Default Order

- discount
- other
- tax

#### Get Cart Conditions Order

```php
$order = Cart::getConditionsOrder();
```

#### Set Cart Conditions Order

```php
Cart::setConditionsOrder([
	'discount',
	'other',
	'tax',
	'shipping',
]);
```

#### Set Items Conditions Order

```php
Cart::setItemsConditionsOrder([
	'discount',
	'other',
	'tax',
	'shipping',
]);
```

> **Note:** Make sure you set Item conditions before adding or updating.

###### Condition targets

You can apply item conditions either on the item price or subtotal.

- price
- subtotal

> **Note 1:** Item conditions are always applied prior to Cart conditions.

> **Note 2:** Price-based item conditions are always applied prior to subtotal-based conditions.

### Conditions Methods

Apply a condition.

```php
Cart::condition(Cartalyst\Conditions\Condition $condition);
```

Return all applied conditions.

```php
Cart::conditions($type|null, bool $includeItems);
```

Set the order in which conditions are applied.

```php
Cart::setConditionsOrder([
	'discount',
	'other',
	'tax',
]);
```

Set the order in which conditions are applied on items.

```php
Cart::setItemsConditionsOrder([
	'discount',
	'tax',
	'shipping',
]);
```

Return all conditions totals grouped by type.

	Cart::conditionsTotal($type);  // Returns an array of results of the type passed including items conditions

	Cart::conditionsTotal($type, false);  // Returns an array of results of the type passed excluding items conditions

	Cart::conditionsTotal();  // Returns an array of results of all applied conditions including items conditions

	Cart::conditionsTotal(null, false);  // Returns an array of results of all applied conditions excluding items conditions

Return the sum of all or a specific type of conditions.

	Cart::conditionsTotalSum($type); // Returns the sum of a the type passed

	Cart::conditionsTotalSum(); // Returns the sum of all conditions

Return all conditions applied only to items.

	Cart::itemsConditions();

Return all or a specific type of items conditions sum grouped by type.

	Cart::itemsConditionsTotal($type); // Returns an array of results of the type passed

	Cart::itemsConditionsTotal(); // Returns an array of results of all applied conditions

Return the sum of all or a specific type of items conditions.

	Cart::itemsConditionsTotalSum($type); // Returns the sum of the type passed

	Cart::itemsConditionsTotalSum(); // Returns the sum of all conditions
