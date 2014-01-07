# Cart v1.0.0

A framework agnostic shopping cart package featuring multiple cart instances, item attributes and conditions.

Part of the Cartalyst Arsenal & licensed [OSI BSD 3](license.txt). Code well, rock on.

## Package Story

History and future capabilities.

### Complete

#### *xx-Jan-14* - v1.0.0

- ```Cart::add($item)``` User can add an item to the cart.
- ```Cart::add($items)``` User can add multiple items to the cart.
- ```Cart::remove($rowId)``` User can remove an item from the cart.
- ```Cart::remove($rowId, $rowId)``` User can remove multiple items from the cart by passing multiple arguments.
- ```Cart::remove(array($rowId, $rowId))``` User can remove multiple items from the cart by passing array.
- ```Cart::update($rowId, $data)``` User can update a single item.
- ```Cart::update($items)``` User can update multiple items.
- ```Cart::update($rowId, $quantity)``` User can update an items quantity.
- ```Cart::items()``` User can return information of all the items that are in the cart.
- ```Cart::item($rowId)``` User can return information of the given item that is on the cart.
- ```Cart::quantity()``` User can return the total # of items that are in the cart.
- ```Cart::subtotal()``` User can return the subtotal of the cart.
- ```Cart::total()``` User can return the cart total.
- ```Cart::taxes()``` User can return all the applied tax rates including item taxes.
- ```Cart::taxes(false)``` User can return all the applied tax rates excluding item taxes.
- ```Cart::taxTotal()``` User can return the cart tax total including item taxes.
- ```Cart::taxTotal(false)``` User can return the cart tax total excluding item taxes.
- ```Cart::itemsTaxes()``` User can return all the taxes applied on items.
- ```Cart::itemsTaxTotal()``` User can return the total of taxes applied on items.
- ```Cart::discounts()``` User can return all the applied discounts.
- ```Cart::discountsTotal()``` User can return the total of applied discounts.
- ```Cart::weight()``` User can return the total cart weight.
- ```Cart::clear()``` User can empty the cart.
- ```Cart::find($data)``` User can search for items that are in the cart.
- ```Cart::find($data, 'instance')``` User can search for items that are in other cart instances.
- ```Cart::instance()``` User can create a new cart instance.
- ```Cart::identify()``` User can return the current cart instance name.
- ```Cart::instances()``` User can return all the created cart instances.
- ```Cart::destroy('instance')``` User can remove a cart instance.
- ```Cart::condition(Cartalyst\Conditions\Condition $condition)``` User can apply a condition.

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
