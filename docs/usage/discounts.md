## Get all the applied discounts

	$discounts = Cart::discounts();
	$discounts = Cart::discounts(false);

## Get the subtotal of the applied discounts

	$discountsSubtotal = Cart::discountsSubtotal();
	$discountsSubtotal = Cart::discountsSubtotal(false);

## Get the total of the applied discounts

	$discountsTotal = Cart::discountsTotal();
	$discountsTotal = Cart::discountsTotal(false);


	Cart::discountedSubtotal();

	Cart::itemsDiscounts();

	Cart::itemsDiscountsTotal();
