# Cart

A framework agnostic shopping cart package featuring multiple cart instances, item attributes and [Conditions](http://www.cartalyst.com/manual/conditions).

Part of the Cartalyst Arsenal & licensed [OSI BSD 3](license.txt). Code well, rock on.

## Package Story

History and future capabilities.

### Complete

#### xx-Jan-14 - v1.0.0

- ```Cart::getIdentity()``` Returns the cart identity.
- ```Cart::setIdentity($name)``` Set the cart identity.
- ```Cart::add($item)``` Adds a single item to the cart.
- ```Cart::add($items)``` Adds multiple items to the cart.
- ```Cart::remove($rowId)``` Removes an item from the cart.
- ```Cart::remove([$rowId, $rowId])``` Removes multiple items from the cart by passing array.
- ```Cart::update($rowId, $data)``` Updates a single item.
- ```Cart::update($items)``` Updates multiple items.
- ```Cart::update($rowId, $quantity)``` Updates an item quantity.
- ```Cart::exists($rowId)``` Check if the given item exists.
- ```Cart::item($rowId)``` Returns information of the given item.
- ```Cart::items()``` Returns information of all items.
- ```Cart::itemsSubtotal()``` Returns the subtotal of the items without conditions.
- ```Cart::quantity()``` Returns the total # of items that are in the cart.
- ```Cart::subtotal()``` Returns the subtotal of the cart.
- ```Cart::total($type|null)``` Returns subtotal after applying upto a specific condition type or null to calculate total.
- ```Cart::weight()``` Returns the total cart weight.
- ```Cart::clear()``` Empty the cart.
- ```Cart::sync(Collection $items)``` Synchronizes a collection of data with the cart.
- ```Cart::find($data)``` Search for items that are in the cart.
- ```Cart::condition(Cartalyst\Conditions\Condition $condition)``` Apply a condition.
- ```Cart::conditions($type|null, bool $includeItems)``` Returns all applied conditions.
- ```Cart::setConditionsOrder($array)``` Sets the order in which conditions are applied.
- ```Cart::setItemsConditionsOrder($array)``` Sets the order in which conditions are applied on items.
- ```Cart::conditionsTotal($type|null, bool $includeItems)``` Returns all conditions totals grouped by type.
- ```Cart::conditionsTotalSum($type|null)``` Returns the sum of all or a specific type of conditions.
- ```Cart::itemsConditions()``` Returns all conditions applied only to items.
- ```Cart::itemsConditionsTotal($type|null)``` Returns all or a specific type of items conditions sum grouped by type.
- ```Cart::itemsConditionsTotalSum($type|null)``` Returns the sum of all or a specific type of items conditions.
- ```Cart::removeCondition($name, bool $includeItems)``` Removes an applied condition by name.
- ```Cart::removeConditions($type|null, bool $includeItems)``` Removes all or a specific type of applied conditions.

## Requirements

- PHP >=5.4

## Installation

Cart is installable with Composer. Read further information on how to install.

[Installation Guide](http://cartalyst.com/manual/cart/introduction/installation)

## Documentation

Refer to the following guide on how to use the Cart package.

[Documentation](http://cartalyst.com/manual/cart)

## Versioning

We version under the [Semantic Versioning](http://semver.org/) guidelines as much as possible.

Releases will be numbered with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

* Breaking backward compatibility bumps the major (and resets the minor and patch)
* New additions without breaking backward compatibility bumps the minor (and resets the patch)
* Bug fixes and misc changes bumps the patch

## Support

Have a bug? Please create an issue here on GitHub that conforms with [necolas's guidelines](https://github.com/necolas/issue-guidelines).

https://github.com/cartalyst/cart/issues

Follow us on Twitter, [@cartalyst](http://twitter.com/cartalyst).

Join us for a chat on IRC.

Server: irc.freenode.net
Channel: #cartalyst
