#Cart v0.9.0

A framework agnostic shopping cart package featuring multiple cart instances and item variants. Part of the Cartalyst Arsenal & licensed [OSI BSD 3](license.md). Code well, rock on.

##Package Story

History and future capabilities.

####Incomplete
- ```Cart::save('instance')``` User can save a cart instance.
- ```Cart::attribute(...)``` User can assign an attribute.

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
	- ```Cart::total()``` User can return cart total.
	- ```Cart::quantity()``` User can return total # of items in cart.
	- ```Cart::find(...)``` User can search for items in cart.
	- ```Cart::find(..., 'instance')``` User can search for items in other cart instances.
	- ```Cart::instance()``` User can create a cart instance.
	- ```Cart::instances()``` User can return all cart instances.
	- ```Cart::destroy('instance')``` User can remove a cart instance.
	- ```Cart::empty('instance')``` User can empty the cart.

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
