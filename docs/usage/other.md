# Other methods

### Grab information of an Item

	$item = Cart::item('027c91341fd5cf4d2579b49c4b6a90da');

### Get all items in the Cart

	$content = Cart::items();

### Destroy Cart instance completely

	Cart::destroy();

### Empty Cart instance

	Cart::clear();

### Get the Cart Subtotal

	$subtotal = Cart::subtotal();

### Get the Cart Total

	$total = Cart::total();

### Get Total number of items in the Cart

	$quantity = Cart::quantity();
