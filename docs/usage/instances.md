

	$whishlist = new Cart('wishlist', $app['cart.storage'], $app['events']);

	$wishlist->add(array(..));


	# we can probably document on how they can create a facade and or a service provider
	# to register their own cart instances and then just alias the cart instance so they
	# can do: Wishlist::add(array(..));
