# Conditions

The Cart package utilizes Cartalyst's Conditions package to manage item and cart based conditions.

The cart has a default set for three types of conditions, discount, tax and other conditions.

## Apply Conditions {#apply-conditions}

### Cart Conditions

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

## Remove Conditions {#remove-conditions}

	Cart::clearConditions($type); // Removes only conditions of $type

	Cart::clearConditions(); // Removes all conditions

## Conditions Types {#conditions-types}

Conditions types are defined by the type property on conditions.

Cart handles discount, other and tax types by default.

> **Note:** If you need to define custom conditions, simply set the Conditions Order by passing an array of types that should be handled by the cart, otherwise only default conditions will be applied.

### Examples

#### Tax

Tax conditions must have the type set to tax

	$condition = new Condition(array(
		'name'   => 'VAT (12.5%)',
		'type'   => 'tax',
		'target' => 'subtotal'
	));

	$condition->setActions(array(
		array('value' => '12.5%')
	));

#### Discount

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

#### Inclusive Conditions

Inclusive conditions are not added to the total but allow you to reverse calculate taxes that are already included in your price.

	$condition = new Condition(array(
		'name'   => 'Tax (5%)',
		'type'   => 'tax',
		'target' => 'subtotal'
	));

	$condition->setActions(array(
		array(
			'value'     => '5%',
			'inclusive' => true,
		)
	));

	// This condition will be reverse calculated and will show up on total conditions methods,
	// but it will not be added to the cart total.

#### Other

Other conditions must have the type set to other

	$condition = new Condition(array(
		'name'   => 'Other (5%)',
		'type'   => 'other',
		'target' => 'subtotal'
	));

	$condition->setActions(array(
		array('value' => '5')
	));

The condition above will add 5 to the subtotal after applying discounts (if any) assuming conditions order are set to their default order.

### Item Conditions

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

## Conditions Order {#conditions-order}

### Default Order

- discount
- other
- tax

### Set Cart Conditions Order

	Cart::setConditionsOrder(array(
		'discount',
		'other',
		'tax',
		'shipping',
	));

### Set Items Conditions Order

	Cart::setItemsConditionsOrder(array(
		'discount',
		'other',
		'tax',
		'shipping',
	));

##### Condition targets

You can apply item conditions either on the item price or subtotal.

- price
- subtotal

> **Note 1:** Item conditions are always applied prior to Cart conditions.

> **Note 2:** Price-based item conditions are always applied prior to subtotal-based conditions.

## Conditions Methods {#conditions-methods}

Apply a condition.

	Cart::condition(Cartalyst\Conditions\Condition $condition);

Return all applied conditions.

	Cart::conditions($type|null, bool $includeItems);

Set the order in which conditions are applied.

	Cart::setConditionsOrder(array(
		'discount',
		'other',
		'tax',
	));

Set the order in which conditions are applied on items.

	Cart::setItemsConditionsOrder(array(
		'discount',
		'tax',
		'shipping',
	));

Return subtotal after applying conditions untill a specific condition type or null to apply all conditions.

	Cart::applyConditions($type); // Returns the subtotal after applying all condition types from the order stack untill it reaches the type passed

	Cart::applyConditions(); // Returns the subtotal after applying all condition types from the order stack untill it reaches the type passed

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

Clear all or a specific type of applied conditions.

	Cart::clearConditions($type); // Clears conditions of the type passed

	Cart::clearConditions(); // Clears all conditions
