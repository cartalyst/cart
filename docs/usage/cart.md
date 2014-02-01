# Cart

Since the whole Cart is a Collection, you have at your disposal quite a few useful
methods to interact and retrieve information from your Cart, however, we added a
few more methods to help you get the job done more quickly.

> Click [here]({url}/usage/collections) to read more about **Collections**.

## Get Total number of items in the Cart {#get-total-items}

	$quantity = Cart::quantity();

## Get all items in the Cart {#get-contents}

	$content = Cart::items();

## Get the subtotal of the items in the Cart

	$itemsSubtotal = Cart::itemsSubtotal();

## Empty the Cart {#clear-cart}

	Cart::clear();

## Get the Cart Subtotal {#get-subtotal}

	$subtotal = Cart::subtotal();

## Get the Cart Total {#get-total}

	$total = Cart::total();

## CRUD Operations

Having the ability to add, remove or update items inside a shopping cart is
crucial job and the Cart got you covered.

Please jump into the next section to read about [Items]({url}/usage/items)
