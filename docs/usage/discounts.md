# Discounts

## Get all the applied discounts including item discounts

	$discounts = Cart::discounts();

## Get all the applied discounts excluding item discounts

	$discounts = Cart::discounts(false);

## Get the subtotal of applied discounts including item discounts

	$subtotal = Cart::discountsSubtotal();

## Get the subtotal of applied discounts excluding item discounts

	$subtotal = Cart::discountsSubtotal(false);

## Get the total of applied discounts including item discounts

	$total = Cart::discountsTotal();

## Get the total of applied discounts excluding item discounts

	$total = Cart::discountsTotal(false);

## Get the subtotal of the cart with the discounts applied

	$subtotal = Cart::discountedSubtotal();

## Get all the discounts applied on items

	$discounts = Cart::itemsDiscounts();

## Get the total of discounts applied on items

	$total = Cart::itemsDiscountsTotal();
