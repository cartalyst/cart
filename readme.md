#Cart v0.9.0

A framework agnostic shopping cart package featuring multiple cart instances and item variants. Part of the Cartalyst Arsenal & licensed [OSI BSD 3](license.md). Code well, rock on.

##Package Story

History and future capabilities.

####Incomplete
- User can save a cart instance.
- User can assign variant label.
- User can assign variant value.
- User can assign variant price.

####Complete
- *15-Aug-13* - v0.9.0
	- ```Cart::add(...)``` User can add an item to cart. 
	- ```Cart::add(...,...)``` User can add multiple items to cart.
	- ```Cart::remove(...)``` User can remove an item from cart.
	- User can remove multiple items from cart.
	- User can update an items quantity.
	- User can update a single item.
	- User can update multiple items.
	- User can return info of item in cart.
	- User can return cart total.
	- User can return total # of items in cart.
	- User can search for items in cart.
	- User can search for items in other cart instances.
	- User can create a cart instance.
	- User can return all cart instances.
	- User can remove a cart instance.
	- User can empty the cart.

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
