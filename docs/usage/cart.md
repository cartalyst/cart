# Cart


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
a crucial job and the Cart has you covered.

Please jump into the next section to read about [Items]({url}/usage/items)
