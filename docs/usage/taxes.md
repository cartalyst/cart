## Get all the applied taxes

	$discounts = Cart::discounts();
	$discounts = Cart::discounts(false);

## Get the subtotal of the applied taxes

	$taxesSubtotal = Cart::taxesSubtotal();
	$taxesSubtotal = Cart::taxesSubtotal(false);

## Get the total of the applied taxes

	$taxesTotal = Cart::taxesTotal();
	$taxesTotal = Cart::taxesTotal(false);


	Cart::itemsTaxes();

	Cart::itemsTaxesTotal();
