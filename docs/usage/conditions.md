# Conditions

The Cart package utilizes Cartalyst's Conditions package to manage item and cart conditions.

The cart has built-in functionality for three types of conditions, tax, discount and regular conditions.

## Condition appliance

Conditions can be applied to either items or to the cart, conditions applied to items may target the item price or subtotal.

### Order of appliance

Conditions are applied in the following order regardless of the order they've been added in.

- Item-based conditions
	- Price-based
		- Discounts
		- Other
		- Taxes
	- Subtotal-based
		- Discounts
		- Other
		- Taxes
- Cart-based
	- Subtotal-based
		- Discounts
		- Other
		- Taxes

### Item-based Conditions

You can add one or more (array) conditions to an item that will be assigned automatically when adding or updating on the cart.

	$condition = new Condition(array(
		'name'   => 'VAT (12.5%)',
		'type'   => 'tax',
		'target' => 'subtotal',
	));

	Cart::add(array(
		'id'         => 'foobar1',
		'name'       => 'Foo Bar 1',
		'quantity'   => 1,
		'price'      => 12.50,
		'conditions' => $condition,
	));

### Cart-based Conditions

You can add one or more (array) conditions to the cart.

	// Single condition
	$condition = new Condition(array(
		'name'   => 'VAT (12.5%)',
		'type'   => 'tax',
		'target' => 'subtotal',
	));

	$condition->setActions(array(
		array('value' => '12.5%')
	));

	Cart::condition($condition);

	// Multiple conditions
	$conditionTax = new Condition(array(
		'name'   => 'VAT (12.5%)',
		'type'   => 'tax',
		'target' => 'subtotal',
	));

	$conditionTax->setActions(array(
		array('value' => '12.5%')
	));

	$conditionDiscount = new Condition(array(
		'name'   => 'Discount (2.5%)',
		'type'   => 'discount',
		'target' => 'subtotal',
	));

	$conditionDiscount->setActions(array(
		array('value' => '-2.5%')
	));

	Cart::condition(array($conditionTax, $conditionDiscount));

## Condition Types

### Tax

Tax conditions must have the type set to tax

	$condition = new Condition(array(
		'name'   => 'VAT (12.5%)',
		'type'   => 'tax',
		'target' => 'subtotal'
	));

	$condition->setActions(array(
		array('value' => '12.5%')
	));

### Discount

Discount conditions must have the type set to discount

	$condition = new Condition(array(
		'name'   => 'Discount (5%)',
		'type'   => 'discount',
		'target' => 'subtotal'
	));

	$condition->setActions(array(
		array('value' => '-5%')
	));

The condition above will apply a 5% discount.

### Regular conditions

Dropping the type key from the condition turns it into a regular condition.

	$condition = new Condition(array(
		'name'   => 'Other (5%)',
		'target' => 'subtotal'
	));

	$condition->setActions(array(
		array('value' => '5')
	));

The condition above will add 5 to the subtotal after applying discounts (if any).
