#Cart v0.9.0

A framework agnostic shopping cart package featuring multiple cart instances and item variants. Part of the Cartalyst Arsenal & licensed [OSI BSD 3](license.md). Code well, rock on.

##Package Story

History and future capabilities.

####Incomplete
- ```Cart::save('instance')``` User can save a cart instance.
- ```Cart::total()``` User can return cart total.
- Items can have attributes. (label, value, price, operation)
- ```Cart::discount(Discount object)``` User can set a discount\coupon object.

####Complete
- *15-Aug-13* - v0.9.0
	- ```Cart::add(...)``` User can add an item to cart.
	- ```Cart::add(...,...)``` User can add multiple items to cart.
	- ```Cart::remove(...)``` User can remove an item from cart.
	- ```Cart::remove(...,...``` User can remove multiple items from cart.
	- ```Cart::update('id', 'quantity')``` User can update an items quantity.
	- ```Cart::update(...)``` User can update a single item.
	- ```Cart::update(...,...)``` User can update multiple items.
	- ```Cart::item('id')``` User can return info of item in cart.
	- ```Cart::items()``` User can return info of all items in cart.
	- ```Cart::subtotal()``` User can return the subtotal of the cart.
	- ```Cart::tax()``` User can return the tax total.
	- ```Cart::weight()``` User can return the total cart weight.
	- ```Cart::quantity()``` User can return total # of items in cart.
	- ```Cart::find(...)``` User can search for items in cart.
	- ```Cart::find(..., 'instance')``` User can search for items in other cart instances.
	- ```Cart::clear()``` User can empty the cart.
	- ```Cart::instance()``` User can create a cart instance.
	- ```Cart::identify()``` User can return the current cart instance name.
	- ```Cart::instances()``` User can return all cart instances.
	- ```Cart::destroy('instance')``` User can remove a cart instance.

Versioning
----------

We version under the [Semantic Versioning](http://semver.org/) guidelines as much as possible.

Releases will be numbered with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

* Breaking backward compatibility bumps the major (and resets the minor and patch)
* New additions without breaking backward compatibility bumps the minor (and resets the patch)
* Bug fixes and misc changes bumps the patch

Support
--------

Have a bug? Please create an issue here on GitHub that conforms with [necolas's guidelines](https://github.com/necolas/issue-guidelines).

https://github.com/cartalyst/cart/issues

Follow us on Twitter, [@cartalyst](http://twitter.com/cartalyst).

Join us for a chat on IRC.

Server: irc.freenode.net
Channel: #cartalyst
