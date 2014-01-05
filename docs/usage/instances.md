# Instances

Cart supports multiple cart instances, so that you can have as many shopping carts on the same page as you want without any kind of conflict.

Here are some examples on how it works

## Creating a new Cart Instance

On this example we'll create a wishlist cart, it will hold all of our customers wishlist items.

	Cart::instance('wishlist');

	Cart::add($item);

	$content = Cart::items();

As you can see the main difference was that we used the `instance()` method before
adding the item, this way Cart knows where we want to save the item.

You probably have noticed that, on the `items()` call we are not using the `instance()` method anymore, thats because once you use the `instance()` method, the other calls will use the instance you used last.

You are probably wondering, how would i go back to the normal cart instance? it's very easy, just use `instance()` method again, and pass in the cart instance name ( default is `main` ), and you should be good to go, example:

	Cart::instance('main');

	$content = Cart::items();

Now you are able to get the main cart content.

## Grabbing all instances

	Cart::instances()
