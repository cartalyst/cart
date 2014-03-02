# Cart v1.0.0

A framework agnostic shopping cart package featuring multiple cart instances, item attributes and [Conditions](http://www.cartalyst.com/manual/conditions).

Part of the Cartalyst Arsenal & licensed [OSI BSD 3](license.txt). Code well, rock on.

## Package Story

History and future capabilities.

### Complete

#### xx-Jan-14 - v1.0.0

- ```Cart::getIdentity()``` Return the cart identity.
- ```Cart::setIdentity($name)``` Set the cart identity.
- ```Cart::add($item)``` Add an item to the cart.
- ```Cart::add($items)``` Add multiple items to the cart.
- ```Cart::remove($rowId)``` Remove an item from the cart.
- ```Cart::remove($rowId, $rowId)``` Remove multiple items from the cart by passing multiple arguments.
- ```Cart::remove(array($rowId, $rowId))``` Remove multiple items from the cart by passing array.
- ```Cart::update($rowId, $data)``` Update a single item.
- ```Cart::update($items)``` Update multiple items.
- ```Cart::update($rowId, $quantity)``` Update an items quantity.
- ```Cart::exists($rowId)``` Check if the given item exists.
- ```Cart::item($rowId)``` Return information of the given item.
- ```Cart::items()``` Return information of all items.
- ```Cart::itemsSubtotal()``` Return the subtotal of the items without conditions.
- ```Cart::quantity()``` Return the total # of items that are in the cart.
- ```Cart::subtotal()``` Return the subtotal of the cart.
- ```Cart::total()``` Return the cart total.
- ```Cart::weight()``` Return the total cart weight.
- ```Cart::clear()``` Empty the cart.
- ```Cart::find($data)``` Search for items that are in the cart.
- ```Cart::condition(Cartalyst\Conditions\Condition $condition)``` Apply a condition.
- ```Cart::conditions($type|null, bool $includeItems)``` Return all applied conditions.
- ```Cart::setConditionsOrder($array)``` Set the order in which conditions are applied.
- ```Cart::setItemsConditionsOrder($array)``` Set the order in which conditions are applied on items.
- ```Cart::applyConditions($type|null)``` Return subtotal after applying upto a specific condition type or null to apply all conditions.
- ```Cart::conditionsTotal($type, bool $includeItems)``` Return all conditions totals grouped by type.
- ```Cart::conditionsTotalSum($type|null)``` Return the sum of all or a specific type of conditions.
- ```Cart::itemsConditions()``` Return all conditions applied only to items.
- ```Cart::itemsConditionsTotal($type|null)``` Return all or a specific type of items conditions sum grouped by type.
- ```Cart::itemsConditionsTotalSum($type|null)``` Return the sum of all or a specific type of items conditions.
- ```Cart::clearConditions($type|null)``` Clear all or a specific type of applied conditions.

## Requirements

- PHP >=5.3

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
